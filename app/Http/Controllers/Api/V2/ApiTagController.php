<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;

class ApiTagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }
    public function store(StoreTagRequest $req)
    {
        return Tag::create($req->validated());
    }
    public function show(Tag $tag)
    {
        return $tag;
    }
    public function update(StoreTagRequest $req, Tag $tag)
    {
        $tag->update($req->validated());
        return $tag;
    }
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return response()->json(null, 204);
    }
}
