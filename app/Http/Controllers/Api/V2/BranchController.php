<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    /**
     * Get all branches
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');
        $search = $request->get('search', '');

        $query = Branch::query();

        // Search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $branches = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

        return response()->json([
            'data' => $branches->items(),
            'meta' => [
                'current_page' => $branches->currentPage(),
                'last_page' => $branches->lastPage(),
                'per_page' => $branches->perPage(),
                'total' => $branches->total(),
            ],
        ]);
    }

    /**
     * Create a new branch
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'code' => 'required|string',
            'address' => 'nullable|array',
            'address.en' => 'nullable|string',
            'address.ar' => 'nullable|string',
            'city' => 'nullable|array',
            'city.en' => 'nullable|string|max:255',
            'city.ar' => 'nullable|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $branch = Branch::create($request->all());
        return response()->json(['data' => $branch], 201);
    }

    /**
     * Get a specific branch
     */
    public function show($id)
    {
        $branch = Branch::findOrFail($id);
        return response()->json(['data' => $branch]);
    }

    /**
     * Update a branch
     */
    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'array',
            'name.en' => 'string|max:255',
            'name.ar' => 'string|max:255',
            'code' => 'string',
            'address' => 'nullable|array',
            'address.en' => 'nullable|string',
            'address.ar' => 'nullable|string',
            'city' => 'nullable|array',
            'city.en' => 'nullable|string|max:255',
            'city.ar' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $branch->update($request->all());
        return response()->json(['data' => $branch]);
    }

    /**
     * Delete a branch
     */
    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();
        return response()->json(['success' => true, 'message' => 'Branch deleted successfully']);
    }

    /**
     * Get branches for a specific product
     */
    public function getProductBranches($productId)
    {
        $product = Product::with('branches')->findOrFail($productId);
        $branches = $product->branches()->active()->get();

        return response()->json(['data' => $branches]);
    }

    /**
     * Update product's requires_branch_selection field
     */
    public function updateBranchRequirement(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'requires_branch_selection' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $product = Product::findOrFail($productId);
        $product->requires_branch_selection = $request->requires_branch_selection;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Branch requirement updated successfully',
            'requires_branch_selection' => $product->requires_branch_selection,
        ]);
    }

    /**
     * Sync branches to a product (many-to-many)
     */
    public function syncProductBranches(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'branch_ids' => 'required|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $product = Product::findOrFail($productId);
        $product->branches()->sync($request->branch_ids);

        return response()->json([
            'success' => true,
            'message' => 'Product branches updated successfully',
            'branches' => $product->branches()->active()->get(),
        ]);
    }
}
