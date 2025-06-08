<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\PostType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApiPostTypesController extends Controller
{
    public function index()
    {
        return PostType::orderBy('id')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug'  => ['required','alpha_dash','unique:post_types,slug'],
            'label' => ['required','string','max:255'],
        ]);

        return response()->json(PostType::create($data), 201);
    }

    public function show(PostType $postType)
    {
        return $postType;
    }

    public function update(Request $request, PostType $postType)
    {
        $data = $request->validate([
            'slug'  => ['required','alpha_dash', Rule::unique('post_types','slug')->ignore($postType->id)],
            'label' => ['required','string','max:255'],
        ]);

        $postType->update($data);
        return $postType;
    }

    public function destroy(PostType $postType)
    {
        $postType->delete();
        return response()->noContent();
    }
}
