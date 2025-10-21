<?php

namespace App\Http\Controllers\Api\V2\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'product'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'fake_reviews' => 'array',
            'fake_reviews.*.name' => 'required|string|max:255',
            'fake_reviews.*.email' => 'required|email|max:255',
            'fake_reviews.*.rating' => 'required|integer|between:1,5',
            'fake_reviews.*.comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $productId = $request->product_id;
        $fakeReviews = $request->fake_reviews ?? [];
        $createdReviews = [];

        DB::transaction(function () use ($fakeReviews, $productId, &$createdReviews) {
            foreach ($fakeReviews as $fakeReview) {
                // Create or find fake user for this review
                $fakeUser = User::firstOrCreate(
                    ['email' => $fakeReview['email']],
                    [
                        'name' => $fakeReview['name'],
                        'email' => $fakeReview['email'],
                        'password' => bcrypt('fake-user-password'),
                        'email_verified_at' => now(),
                        'is_fake' => true, // Add this field to users table if needed
                    ]
                );

                // Create the review
                $review = Review::create([
                    'product_id' => $productId,
                    'user_id' => $fakeUser->id,
                    'rating' => $fakeReview['rating'],
                    'comment' => $fakeReview['comment'],
                    'status' => 1, // approved
                    'viewed' => 0,
                ]);

                $createdReviews[] = $review->load('user');
            }
        });

        return response()->json([
            'success' => true,
            'message' => count($createdReviews) . ' fake reviews created successfully',
            'data' => $createdReviews,
        ], 201);
    }

    public function show(Review $review)
    {
        $review->load(['user', 'product']);

        return response()->json([
            'success' => true,
            'data' => $review,
        ]);
    }

    public function update(Request $request, Review $review)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|integer|between:1,5',
            'comment' => 'sometimes|string|max:1000',
            'status' => 'sometimes|integer|in:0,1',
            'user_name' => 'sometimes|string|max:255',
            'user_email' => 'sometimes|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update review fields
        $review->update($request->only(['rating', 'comment', 'status']));

        // Update user name and email if provided (for fake reviews)
        if ($request->has('user_name') || $request->has('user_email')) {
            $userUpdateData = [];

            if ($request->has('user_name')) {
                $userUpdateData['name'] = $request->user_name;
            }

            if ($request->has('user_email')) {
                // Check if email is already taken by another user
                $existingUser = User::where('email', $request->user_email)
                    ->where('id', '!=', $review->user_id)
                    ->first();

                if ($existingUser) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email already exists for another user',
                    ], 422);
                }

                $userUpdateData['email'] = $request->user_email;
            }

            if (!empty($userUpdateData)) {
                $review->user->update($userUpdateData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => $review->fresh(['user', 'product']),
        ]);
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully',
        ]);
    }

    public function getProductReviews(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'review_ids' => 'required|array',
            'review_ids.*' => 'exists:reviews,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $deletedCount = Review::whereIn('id', $request->review_ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} reviews deleted successfully",
        ]);
    }

    public function toggleStatus(Review $review)
    {
        $review->update([
            'status' => $review->status ? 0 : 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review status updated successfully',
            'data' => $review->fresh(['user', 'product']),
        ]);
    }
}