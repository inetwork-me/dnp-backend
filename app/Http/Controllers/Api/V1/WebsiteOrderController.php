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

    /** POST /api/checkout **/
    public function store(Request $request)
    {
        $data = $request->validate([
            'cart_id'           => 'required|exists:carts,id',
            'shipping_address'  => 'required|array',
            'billing_address'   => 'array|nullable',
            'payment_method'    => 'string|nullable',
            // require a guest email if there’s no authenticated user
            'guest_email'       => 'required_without:auth|email',
            'guest_name'       => 'required_without:auth|string',
        ]);

        $cart = Cart::with('items')->findOrFail($data['cart_id']);
        abort_if($cart->items->isEmpty(), 400, 'Cart is empty.');

        // 1) Determine or create the user
        $user = $request->user();
        if (!$user) {
            // if email already exists, use that account; else make a new one
            $user = User::firstOrCreate(
                ['email' => $data['guest_email']],
                [
                    'password' => Hash::make(Str::random(12)),
                    'name'     => $data['guest_name'],
                ]
            );

            // attach the cart to this new user
            $cart->user()->associate($user);
            $cart->save();
        }

        // 2) Snapshot cart → order
        return DB::transaction(function () use ($cart, $data, $user) {
            $order = Order::create([
                'user_id'          => $user->id,
                'cart_id'          => $cart->id,
                'order_number'     => now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'status'           => 'pending',
                'total_amount'     => $cart->items->sum(fn ($i) => $i->quantity * $i->unit_price),
                'shipping_address' => $data['shipping_address'],
                'billing_address'  => $data['billing_address'] ?? $data['shipping_address'],
                'payment_method'   => $data['payment_method'] ?? null,
                'payment_status'   => 'unpaid',
            ]);

            foreach ($cart->items as $ci) {
                $order->items()->create([
                    'product_id' => $ci->product_id,
                    'quantity'   => $ci->quantity,
                    'unit_price' => $ci->unit_price,
                    'line_total' => $ci->quantity * $ci->unit_price,
                    'options'    => $ci->options,
                ]);
            }

            $cart->update(['status' => 'converted']);

            return $order->load('items.product');
        });
    }
}
