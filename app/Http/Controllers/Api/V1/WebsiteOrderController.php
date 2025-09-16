<?php

// app/Http/Controllers/WebsiteOrderController.php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Illuminate\Support\Facades\Hash;

class WebsiteOrderController extends Controller
{
    protected $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->loyaltyService = $loyaltyService;
    }
    // GET /api/orders
    public function index(Request $request)
    {
        return Order::where('user_id', $request->user()->id)
            ->with(['items.product', 'latestShipment'])
            ->get();
    }

    // GET /api/orders/{order}
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return $order->load('items.product');
    }

    public function store(Request $request)
    {
        $shippingEnabled = is_shipping_enabled();

        $validationRules = [
            'cart_id'           => 'required|exists:carts,id',
            'billing_address'   => 'array|nullable',
            'payment_method'    => 'string|nullable',
            'guest_email'       => 'required_without:auth|email',
            'guest_name'        => 'required_without:auth|string',
        ];

        // Add shipping validation only if shipping is enabled
        if ($shippingEnabled) {
            $validationRules['shipping_address'] = 'nullable|array';
            $validationRules['shipping_method_id'] = 'nullable';
            $validationRules['shipping_cost'] = 'nullable|numeric|min:0';
            $validationRules['shipping_quote_data'] = 'nullable|array';
        }

        $data = $request->validate($validationRules);

        // 1) load cart + applied coupon
        $cart = Cart::with(['items.product', 'coupon'])
            ->findOrFail($data['cart_id']);
        abort_if($cart->items->isEmpty(), 400, 'Cart is empty.');

        // 2) find or create user
        $user = $request->user();
        if (!$user) {
            $user = \App\Models\User::firstOrCreate(
                ['email' => $data['guest_email']],
                [
                    'password' => Hash::make(Str::random(12)),
                    'name'     => $data['guest_name'],
                ]
            );
            $cart->user()->associate($user);
            $cart->save();
        }

        // 3) snapshot cart → order
        $order = DB::transaction(function () use ($cart, $data, $user, $shippingEnabled) {
            // a) compute amounts
            $subtotal = $cart->items->sum(fn ($i) => $i->quantity * $i->unit_price);
            $discount = $cart->coupon
                ? $cart->coupon->calculateDiscount($subtotal)
                : 0;

            // Only add shipping cost if shipping is enabled
            $shippingCost = 0;
            if ($shippingEnabled && isset($data['shipping_cost'])) {
                $shippingCost = $data['shipping_cost'];
            }

            $total = max(0, $subtotal - $discount + $shippingCost);

            // Handle shipping method ID for live rates only if shipping is enabled
            $shippingMethodId = null;
            if ($shippingEnabled && isset($data['shipping_method_id'])) {
                $methodId = $data['shipping_method_id'];
                // Check if it's a live rate (starts with 'live_')
                if (is_string($methodId) && str_starts_with($methodId, 'live_')) {
                    // For live rates, set shipping_method_id to null
                    // The full shipping info is stored in shipping_quote_data
                    $shippingMethodId = null;
                } else {
                    // For fixed shipping methods, use the method ID
                    $shippingMethodId = is_numeric($methodId) ? (int)$methodId : null;
                }
            }

            // b) create the order — **note** the use of 'total_amount' here
            $order = Order::create([
                'user_id'          => $user->id,
                'cart_id'          => $cart->id,
                'order_number'     => now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'status'           => 'pending',

                // pricing fields — adjust names to match your table:
                'subtotal'         => round($subtotal, 2),
                'discount'         => round($discount, 2),
                'total_amount'     => round($total, 2),
                'coupon_id'        => $cart->coupon_id,

                // shipping fields (only if shipping enabled)
                'shipping_method_id'    => $shippingMethodId,
                'shipping_cost'         => round($shippingCost, 2),
                'shipping_quote_data'   => $shippingEnabled ? ($data['shipping_quote_data'] ?? null) : null,

                'shipping_address' => $shippingEnabled ? ($data['shipping_address'] ?? null) : null,
                'billing_address'  => $data['billing_address'] ?? ($shippingEnabled ? ($data['shipping_address'] ?? null) : null),
                'payment_method'   => $data['payment_method'] ?? null,
                'payment_status'   => 'unpaid',
            ]);

            // c) copy each cart item
            foreach ($cart->items as $ci) {
                $order->items()->create([
                    'product_id' => $ci->product_id,
                    'quantity'   => $ci->quantity,
                    'unit_price' => $ci->unit_price,
                    'line_total' => $ci->quantity * $ci->unit_price,
                    'options'    => $ci->options,
                ]);
            }

            // d) record coupon redemption + bump global counter
            if ($cart->coupon) {
                DB::transaction(function () use ($cart, $order, $discount) {
                    $cart->coupon->redemptions()->create([
                        'user_id'  => optional(request()->user())->id,
                        'cart_id'  => $cart->id,
                        'order_id' => $order->id,
                        'discount' => $discount,
                    ]);
                    $cart->coupon->increment('times_used');
                });
            }

            // e) close out the cart
            $cart->update(['status' => 'converted']);

            return $order;
        });

        // 4) Process loyalty points after successful order creation
        try {
            $this->loyaltyService->processOrderLoyaltyPoints($order);
        } catch (\Exception $e) {
            // Log error but don't fail the order
            \Log::error('Loyalty points processing failed for order ' . $order->id . ': ' . $e->getMessage());
        }

        // 5) return with items & coupon
        return response()->json(
            $order->load('items.product', 'coupon'),
            201
        );
    }
}
