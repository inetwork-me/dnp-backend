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
        $this->middleware('auth');  // only signed-in users
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
}
