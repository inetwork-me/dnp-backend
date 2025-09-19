<?php

// app/Http/Controllers/WebsiteOrderController.php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
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
            'guest_phone'       => 'string|nullable',
            'coupon_code'       => 'string|nullable',
            'voucher_code'      => 'string|nullable',
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

        // 1.5) Apply coupon if provided and not already applied
        if (!empty($data['coupon_code']) && (!$cart->coupon || $cart->coupon->code !== $data['coupon_code'])) {
            $coupon = Coupon::where('code', $data['coupon_code'])
                ->where('active', true)
                ->first();

            if (!$coupon) {
                abort(400, 'Invalid coupon code');
            }

            // Check if coupon is valid (not expired, usage limits, etc.)
            if ($coupon->ends_at && now()->isAfter($coupon->ends_at)) {
                abort(400, 'Coupon has expired');
            }

            if ($coupon->starts_at && now()->isBefore($coupon->starts_at)) {
                abort(400, 'Coupon is not yet active');
            }

            // Apply coupon to cart
            $cart->update(['coupon_id' => $coupon->id]);
            $cart->load('coupon'); // Reload with coupon relationship
        }

        // 2) find or create user
        $user = $request->user();
        $isGuestUser = false;
        $guestPassword = null;

        if (!$user) {
            $guestPassword = Str::random(12);
            $user = \App\Models\User::firstOrCreate(
                ['email' => $data['guest_email']],
                [
                    'password' => Hash::make($guestPassword),
                    'name'     => $data['guest_name'],
                    'phone'    => $data['guest_phone'] ?? null,
                ]
            );
            $cart->user()->associate($user);
            $cart->save();
            $isGuestUser = true;
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

        // 4) Handle guest user account setup
        if ($isGuestUser && $guestPassword) {
            try {
                // Create customer profile for guest user
                $customer = $user->getOrCreateCustomer();

                // Update customer with billing address information
                $customerUpdateData = [];

                if (isset($data['billing_address'])) {
                    $customerUpdateData = [
                        'billing_address' => $data['billing_address']['line1'] ?? null,
                        'billing_city' => $data['billing_address']['city'] ?? null,
                        'billing_state' => $data['billing_address']['state'] ?? null,
                        'billing_country' => $data['billing_address']['country'] ?? null,
                        'billing_postal_code' => $data['billing_address']['postal_code'] ?? null,
                    ];
                }

                // Update customer with shipping information if available
                if (isset($data['shipping_address'])) {
                    $customerUpdateData = array_merge($customerUpdateData, [
                        'shipping_address' => $data['shipping_address']['line1'] ?? null,
                        'shipping_city' => $data['shipping_address']['city'] ?? null,
                        'shipping_state' => $data['shipping_address']['state'] ?? null,
                        'shipping_country' => $data['shipping_address']['country'] ?? null,
                        'shipping_postal_code' => $data['shipping_address']['postal_code'] ?? null,
                    ]);
                }

                if (!empty($customerUpdateData)) {
                    $customer->update($customerUpdateData);
                }

                // Send welcome email with login credentials
                $user->notify(new \App\Notifications\GuestUserWelcomeNotification($user, $guestPassword, $order));

            } catch (\Exception $e) {
                // Log error but don't fail the order
                \Log::error('Guest user setup failed for order ' . $order->id . ': ' . $e->getMessage());
            }
        }

        // 5) Process loyalty points after successful order creation
        try {
            $this->loyaltyService->processOrderLoyaltyPoints($order);
        } catch (\Exception $e) {
            // Log error but don't fail the order
            \Log::error('Loyalty points processing failed for order ' . $order->id . ': ' . $e->getMessage());
        }

        // 6) return with items & coupon
        return response()->json(
            $order->load('items.product', 'coupon'),
            201
        );
    }
}
