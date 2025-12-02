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
        // Try to get user from Bearer token if present (even though route is public)
        $user = $request->user('sanctum');

        // Use user_id from query param if provided, otherwise use authenticated user
        $userId = $request->input('user_id') ?? ($user ? $user->id : null);
        $guestToken = $request->header('X-Guest-Token');


        // Create or fetch the open cart, and eager-load items + coupon in one go
        if ($userId) {
            // Authenticated user - use user_id
            $cart = Cart::firstOrCreate(
                ['user_id' => $userId, 'status' => 'open']
            );
        } else if ($guestToken) {
            // Guest user - use guest_token
            // First try to find existing cart
            $cart = Cart::where('guest_token', $guestToken)
                ->where('status', 'open')
                ->whereNull('user_id')
                ->first();

            // If no cart found, create one
            if (!$cart) {
                $cart = Cart::create([
                    'guest_token' => $guestToken,
                    'user_id' => null,
                    'status' => 'open',
                ]);
            }
        } else {
            // No token provided - return empty cart
            return response()->json([
                'cart' => null,
                'items_count' => 0,
                'subtotal' => 0,
                'coupon' => null,
                'discount' => 0,
                'total_price' => 0,
            ]);
        }

        $cart->load('items.product', 'items.branch', 'coupon');

        // Calculate counts & raw subtotal using discounted prices
        $totalCount = $cart->items->sum('quantity');
        $subtotal   = $cart->items->sum(fn ($i) => $i->quantity * home_discounted_base_price($i->product, false));

        $discount = 0;
        $coupon   = $cart->coupon;     // null if none applied

        if ($coupon) {
            // Use your Coupon model’s validity check
            if ($coupon->isValidForUser($request->user('sanctum'))) {
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
            'user_id'    => 'nullable|exists:users,id',
            'branch_id'  => 'nullable|exists:branches,id',
        ]);

        // Use user_id from request body if provided, otherwise check auth
        $userId = $data['user_id'] ?? optional($request->user('sanctum'))->id;
        $guestToken = $request->header('X-Guest-Token');

        // Find or create cart based on auth status
        if ($userId) {
            $cart = Cart::firstOrCreate([
                'user_id' => $userId,
                'status'  => 'open',
            ]);
        } else if ($guestToken) {
            // First try to find existing cart
            $cart = Cart::where('guest_token', $guestToken)
                ->where('status', 'open')
                ->whereNull('user_id')
                ->first();

            // If no cart found, create one
            if (!$cart) {
                $cart = Cart::create([
                    'guest_token' => $guestToken,
                    'user_id' => null,
                    'status' => 'open',
                ]);
            }
        } else {
            abort(400, 'Guest token is required for unauthenticated requests');
        }

        $product = Product::findOrFail($data['product_id']);

        // Build unique constraint - include branch_id if provided
        $uniqueConstraint = ['product_id' => $product->id];
        if (isset($data['branch_id'])) {
            $uniqueConstraint['branch_id'] = $data['branch_id'];
        }

        $item = $cart->items()->updateOrCreate(
            $uniqueConstraint,
            [
                'quantity'   => $data['quantity'],
                'unit_price' => $product->unit_price,
                'options'    => $data['options'] ?? [],
                'branch_id'  => $data['branch_id'] ?? null,
            ]
        );

        return response()->json($item->load('product', 'branch'));
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
     * DELETE /api/cart/items/{item_id}
     * Remove an item from cart with proper validation
     */
    public function removeItem(Request $request, $item_id)
    {
        // Find the cart item with its cart relationship
        $item = CartItem::with('cart')->find($item_id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found'
            ], 404);
        }

        // Get the cart that this item belongs to
        $cart = $item->cart;

        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'Cart not found for this item'
            ], 404);
        }

        // Get current user/guest token
        // Try to authenticate with Sanctum Bearer token if present
        $user = $request->user('sanctum');
        $userId = $user ? $user->id : null;
        $guestToken = $request->header('X-Guest-Token');

        // Verify ownership: either by user_id or guest_token
        $hasAccess = false;

        if ($userId && $cart->user_id && (int)$cart->user_id === (int)$userId) {
            // Authenticated user owns this cart
            $hasAccess = true;
        } elseif ($guestToken && $cart->guest_token === $guestToken && $cart->user_id === null) {
            // Guest user owns this cart
            $hasAccess = true;
        } elseif (!$userId && !$guestToken) {
            // No authentication provided at all
            return response()->json([
                'success' => false,
                'message' => 'Authentication required. Please provide user token or guest token.',
                'debug' => [
                    'has_user' => $userId ? true : false,
                    'has_guest_token' => $guestToken ? true : false,
                ]
            ], 401);
        }

        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to remove this item',
                'debug' => [
                    'cart_user_id' => $cart->user_id,
                    'cart_user_id_type' => gettype($cart->user_id),
                    'cart_guest_token' => $cart->guest_token ? substr($cart->guest_token, 0, 10) . '...' : 'null',
                    'request_user_id' => $userId,
                    'request_user_id_type' => gettype($userId),
                    'request_guest_token' => $guestToken ? substr($guestToken, 0, 10) . '...' : 'null',
                    'user_match' => $userId && $cart->user_id && (int)$cart->user_id === (int)$userId,
                    'guest_match' => $guestToken && $cart->guest_token === $guestToken,
                ]
            ], 403);
        }

        // Delete the item
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart successfully'
        ], 200);
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
        $user = $request->user('sanctum'); // Get current authenticated user (if any)
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

    /**
     * POST /api/v1/cart/transfer-ownership
     *
     * Transfers a guest cart to an authenticated user.
     * Simply updates the cart's user_id and removes guest_token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transferOwnership(Request $request)
    {
        $validated = $request->validate([
            'cart_id' => 'required|integer|exists:carts,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        $cartId = $validated['cart_id'];
        $userId = $validated['user_id'];

        // Find the cart
        $cart = Cart::findOrFail($cartId);

        // Security: Only allow transfer if cart is guest cart (no user_id)
        if ($cart->user_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Cart already belongs to a user'
            ], 400);
        }

        // Security: Only allow transfer if cart is open
        if ($cart->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Only open carts can be transferred'
            ], 400);
        }

        // Transfer ownership: Set user_id and clear guest_token
        $cart->update([
            'user_id' => $userId,
            'guest_token' => null,
        ]);

        // Load cart with items and return
        $cart->load('items.product', 'items.branch', 'coupon');

        return response()->json([
            'success' => true,
            'message' => 'Cart ownership transferred successfully',
            'cart' => $cart,
        ], 200);
    }

}
