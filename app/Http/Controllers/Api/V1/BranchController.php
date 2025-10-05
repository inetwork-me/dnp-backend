<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Get all active branches
     */
    public function index(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');

        $branches = Branch::active()->get()->map(function ($branch) use ($locale) {
            return [
                'id' => $branch->id,
                'name' => $branch->getName($locale),
                'code' => $branch->code,
                'address' => $branch->address,
                'city' => $branch->city,
                'phone' => $branch->phone,
                'email' => $branch->email,
            ];
        });

        return response()->json([
            'success' => true,
            'branches' => $branches,
        ]);
    }

    /**
     * Get available branches for a specific product
     */
    public function getProductBranches(Request $request, $productId)
    {
        $locale = $request->header('Accept-Language', 'en');
        $product = Product::with('branches')->findOrFail($productId);

        // If product doesn't require branch selection
        if (!$product->requires_branch_selection) {
            return response()->json([
                'success' => true,
                'requires_branch' => false,
                'branches' => [],
            ]);
        }

        // Get branches associated with this product
        $branches = $product->branches()->active()->get()->map(function ($branch) use ($locale) {
            return [
                'id' => $branch->id,
                'name' => $branch->getName($locale),
                'code' => $branch->code,
                'address' => $branch->address,
                'city' => $branch->city,
                'phone' => $branch->phone,
            ];
        });

        return response()->json([
            'success' => true,
            'requires_branch' => true,
            'branches' => $branches,
        ]);
    }
}
