<?php

// app/Http/Controllers/WebsiteOrderController.php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Illuminate\Support\Facades\Hash;

class WebsiteOrderController extends Controller
{
    // GET /api/orders
    public function index(Request $request)
    {
        return Order::where('user_id', $request->user()->id)
            ->with('items.product')
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
        $data = $request->validate([
            'cart_id'           => 'required|exists:carts,id',
            'shipping_address'  => 'required|array',
            'billing_address'   => 'array|nullable',
            'payment_method'    => 'string|nullable',
            'guest_email'       => 'required_without:auth|email',
            'guest_name'        => 'required_without:auth|string',
        ]);

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
        $order = DB::transaction(function () use ($cart, $data, $user) {
            // a) compute amounts
            $subtotal = $cart->items->sum(fn ($i) => $i->quantity * $i->unit_price);
            $discount = $cart->coupon
                ? $cart->coupon->calculateDiscount($subtotal)
                : 0;
            $total    = max(0, $subtotal - $discount);

            // b) create the order — **note** the use of 'total_amount' here
            $order = Order::create([
                'user_id'          => $user->id,
                'cart_id'          => $cart->id,
                'order_number'     => now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'status'           => 'pending',

                // pricing fields — adjust names to match your table:
                'subtotal'         => round($subtotal, 2),
                'discount'         => round($discount, 2),
                'total_amount'     => round($total, 2),    // ← was missing
                'coupon_id'        => $cart->coupon_id,

                'shipping_address' => $data['shipping_address'],
                'billing_address'  => $data['billing_address'] ?? $data['shipping_address'],
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

        // 4) return with items & coupon
        return response()->json(
            $order->load('items.product', 'coupon'),
            201
        );
    }
}
