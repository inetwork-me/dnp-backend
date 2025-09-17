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
        $wishlist = Wishlist::where('user_id', $request->user()->id)
            ->with(['product:id,name,slug,thumbnail_img,price,discount_price,label'])
            ->get();

        return response()->json([
            'success' => true,
            'items' => $wishlist->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'thumbnail_img' => $item->product->thumbnail_img,
                    'price' => $item->product->price,
                    'discount_price' => $item->product->discount_price,
                    'label' => $item->product->label,
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
