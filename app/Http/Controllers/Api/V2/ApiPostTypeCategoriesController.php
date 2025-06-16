<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\PostType;
use App\Models\PostTypeCategory;
use Illuminate\Http\Request;

class ApiPostTypeCategoriesController extends Controller
{
    // GET  /api/v2/post-types/{postType}/categories
    public function index(PostType $postType)
    {
        return $postType
            ->categories()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // POST /api/v2/post-types/{postType}/categories
    public function store(Request $request, PostType $postType)
    {
        $data = $request->validate([
            'name'      => ['required', 'array'],
            'name.en'   => ['required', 'string', 'max:255'],
            'name.ar'   => ['required', 'string', 'max:255'],
            'slug'      => ['required', 'string'],

        ]);

        // automatically sets post_type_id from the route
        $category = $postType->categories()->create($data);

        return response()->json($category, 201);
    }

    // GET    /api/v2/post-types/{postType}/categories/{category}
    public function show(PostType $postType, PostTypeCategory $category)
    {
        // you may want to guard that $category->post_type_id == $postType->id
        return $category;
    }

    // PUT/PATCH /api/v2/post-types/{postType}/categories/{category}
    public function update(Request $request, PostType $postType, PostTypeCategory $category)
    {
        $data = $request->validate([
            'name'      => ['sometimes', 'array'],
            'name.en'   => ['sometimes', 'string', 'max:255'],
            'name.ar'   => ['sometimes', 'string', 'max:255'],
        ]);

        $category->update($data);

        return $category;
    }

    // DELETE /api/v2/post-types/{postType}/categories/{category}
    public function destroy(PostType $postType, PostTypeCategory $category)
    {
        $category->delete();
        return response()->noContent();
    }
}
