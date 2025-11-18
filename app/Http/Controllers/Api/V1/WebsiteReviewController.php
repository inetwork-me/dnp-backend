<?php
// app/Http/Controllers/WebsiteReviewController.php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class WebsiteReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');  // only signed-in users
    }

    public function index(Request $request)
    {
        $reviews = Review::where('user_id', $request->user()->id)
            ->with(['product:id,name,slug,thumbnail_img'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
        ]);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'product_id' => ['required', 'exists:products,id'],
            'rating'     => ['required', 'integer', 'between:1,5'],
            'comment'    => ['nullable', 'string'],
        ]);

        $data['user_id'] = $req->user()->id;
        $data['status']  = 1;  // or whatever your workflow is
        $data['viewed']  = false;

        $review = Review::create($data);

        return response()->json([
            'message' => 'Review submitted',
            'review'  => $review->load('user'),
        ], 201);
    }

    /**
     * Update the user's own review
     */
    public function update(Request $req, $id)
    {
        $review = Review::where('id', $id)
            ->where('user_id', $req->user()->id)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found or you do not have permission to edit this review'
            ], 404);
        }

        $data = $req->validate([
            'rating'  => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
        ]);

        $review->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'review'  => $review->fresh()->load('user', 'product:id,name,slug,thumbnail_img'),
        ]);
    }

    /**
     * Delete the user's own review
     */
    public function destroy(Request $req, $id)
    {
        $review = Review::where('id', $id)
            ->where('user_id', $req->user()->id)
            ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found or you do not have permission to delete this review'
            ], 404);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ]);
    }
}
