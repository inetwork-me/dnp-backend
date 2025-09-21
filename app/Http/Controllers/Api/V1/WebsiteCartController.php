<?php

// app/Http/Controllers/Api/V1/WebsiteCartController.php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;                // NEW: bring in the Coupon model
use Illuminate\Http\Request;
use Carbon\Carbon;                     // NEW: for date comparisons

class WebsiteCartController extends Controller
{
    /** 
     * GET /api/cart 
     * 
     * Returns the current open cart, its items, totals, 
     * and—if applied—a coupon, discount, and adjusted total.
     */
    public function current(Request $request)
    {
        $userId = optional($request->user())->id;

        // Create or fetch the open cart, and eager-load items + coupon in one go
        $cart = Cart::firstOrCreate(
            ['user_id' => $userId, 'status' => 'open']
        );
        $cart->load('items.product', 'coupon');

        // Calculate counts & raw subtotal using discounted prices
        $totalCount = $cart->items->sum('quantity');
        $subtotal   = $cart->items->sum(fn ($i) => $i->quantity * home_discounted_base_price($i->product, false));

        $discount = 0;
        $coupon   = $cart->coupon;     // null if none applied

        if ($coupon) {
            // Use your Coupon model’s validity check
            if ($coupon->isValidForUser($request->user())) {
                $discount = $coupon->calculateDiscount($subtotal);
            } else {
                // detach invalid coupon
                $cart->update(['coupon_id' => null]);
                $coupon   = null;
                $discount = 0;
            }
        }

        $totalPrice = max(0, $subtotal - $discount);

        return response()->json([
            'cart'         => $cart,
            'items_count'  => $totalCount,
            'subtotal'     => $subtotal,
            'coupon'       => $coupon
                ? $coupon->only(['code', 'type', 'value', 'starts_at', 'ends_at'])
                : null,
            'discount'     => round($discount, 2),
            'total_price'  => round($totalPrice, 2),
        ]);
    }

    /** 
     * POST /api/cart/items 
     * (unchanged) 
     */
    public function addItem(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'options'    => 'array|nullable',
        ]);

        $userId = optional($request->user())->id;
        $cart = Cart::firstOrCreate([
            'user_id' => $userId,
            'status'  => 'open',
        ]);

        $product = Product::findOrFail($data['product_id']);

        $item = $cart->items()->updateOrCreate(
            ['product_id' => $product->id],
            [
                'quantity'   => $data['quantity'],
                'unit_price' => $product->unit_price,
                'options'    => $data['options'] ?? [],
            ]
        );

        return response()->json($item->load('product'));
    }

    /** 
     * PUT /api/cart/items/{item} 
     * (unchanged) 
     */
    public function updateItem(Request $request, CartItem $item)
    {
        $data = $request->validate([
            'quantity' => 'integer|min:1',
            'options'  => 'array|nullable',
        ]);

        $item->update($data);
        return $item->load('product');
    }

    /** 
     * DELETE /api/cart/items/{item} 
     * (unchanged) 
     */
    public function removeItem(CartItem $item)
    {
        $item->delete();
        return response()->noContent();
    }

    /**
     * POST /api/cart/{cart}/apply-coupon
     * 
     * Validates and attaches a coupon to the cart.
     */
    public function applyCoupon(Request $request, Cart $cart)
    {
        $request->validate([
            'code' => 'required|string|exists:coupons,code',
        ]);

        $coupon = Coupon::where('code', $request->code)->firstOrFail();

        // Use the model's comprehensive validation method
        $user = $request->user(); // Get current authenticated user (if any)
        if (!$coupon->isValidForUser($user)) {
            // Determine specific error message
            if ($coupon->ends_at && Carbon::now()->gt($coupon->ends_at)) {
                $message = 'Coupon has expired';
            } elseif ($coupon->starts_at && Carbon::now()->lt($coupon->starts_at)) {
                $message = 'Coupon is not yet active';
            } elseif ($coupon->usage_limit_global && $coupon->redemptions()->count() >= $coupon->usage_limit_global) {
                $message = 'Coupon usage limit reached';
            } elseif ($coupon->usage_limit_per_customer && $user && $coupon->redemptions()->where('user_id', $user->id)->count() >= $coupon->usage_limit_per_customer) {
                $message = 'You have already used this coupon the maximum number of times';
            } else {
                $message = 'Coupon is not valid';
            }
            return response()->json(['message' => $message], 422);
        }

        // 3. Calculate discount
        $subtotal = $cart->items->sum(fn ($i) => $i->quantity * $i->product->unit_price);
        $discount = $coupon->type === 'percent'
            ? $subtotal * ($coupon->value / 100)
            : min($coupon->value, $subtotal);

        // 4. Attach coupon to cart (don't increment usage until order is placed)
        $cart->update([
            'coupon_id' => $coupon->id
        ]);

        return response()->json([
            'coupon'   => $coupon->only(['code', 'type', 'value', 'ends_at']),
            'discount' => round($discount, 2),
            'total'    => round(max(0, $subtotal - $discount), 2),
        ]);
    }

    /**
     * DELETE /api/cart/{cart}/remove-coupon
     * 
     * Detaches any coupon from the cart.
     */
    public function removeCoupon(Cart $cart)
    {
        $cart->update(['coupon_id' => null]);
        return response()->noContent();
    }

}
