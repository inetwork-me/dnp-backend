<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    // Get user's wishlist
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $wishlist = Wishlist::where('user_id', $userId)
            ->with(['product:id,name,slug,thumbnail_img,unit_price,discount,discount_type,label,current_stock,category_id,thumbnail', 'product.main_category:id,name'])
            ->get();

        // Get user's active cart product IDs
        $cartProductIds = \App\Models\Cart::where('user_id', $userId)
            ->where('status', 'open')
            ->with('items:cart_id,product_id')
            ->first()
            ?->items
            ->pluck('product_id')
            ->toArray() ?? [];

        return response()->json([
            'success' => true,
            'items' => $wishlist->map(function ($item) use ($cartProductIds) {
                return [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'thumbnail_img' => $item->product->thumbnail_img,
                    'unit_price' => $item->product->unit_price,
                    'discount' => $item->product->discount,
                    'discount_type' => $item->product->discount_type,
                    'label' => $item->product->label,
                    'current_stock' => $item->product->current_stock,
                    'thumbnail' => $item->product->thumbnail,
                    'isAddedToCart' => in_array($item->product->id, $cartProductIds),
                    'category' => $item->product->main_category ? [
                        'id' => $item->product->main_category->id,
                        'name' => $item->product->main_category->name,
                        'label' => $item->product->main_category->name, // Using name as label fallback
                    ] : null,
                ];
            })
        ]);
    }

    // Add product to wishlist
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $wishlistItem = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $request->product_id
        ]);

        if ($wishlistItem->wasRecentlyCreated) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product already in wishlist'
        ]);
    }

    // Remove product from wishlist
    public function destroy(Request $request, $productId)
    {
        $deleted = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $productId)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found in wishlist'
        ], 404);
    }

    // Clear entire wishlist
    public function clear(Request $request)
    {
        Wishlist::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared'
        ]);
    }

    // Sync local wishlist with server
    public function sync(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'integer|exists:products,id'
        ]);

        $userId = $request->user()->id;
        $productIds = $request->items;

        // Get current wishlist from database
        $currentWishlist = Wishlist::where('user_id', $userId)->pluck('product_id')->toArray();

        // Items to add (in local but not in database)
        $toAdd = array_diff($productIds, $currentWishlist);

        // Items to remove (in database but not in local)
        $toRemove = array_diff($currentWishlist, $productIds);

        // Add missing items
        foreach ($toAdd as $productId) {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
        }

        // Remove extra items
        if (!empty($toRemove)) {
            Wishlist::where('user_id', $userId)
                ->whereIn('product_id', $toRemove)
                ->delete();
        }

        // Return updated wishlist
        return $this->index($request);
    }
}
