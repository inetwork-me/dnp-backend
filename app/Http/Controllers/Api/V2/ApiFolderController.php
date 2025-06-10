<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFolderRequest;
use App\Models\MediaFolder;
use Illuminate\Http\Request;

class ApiFolderController extends Controller
{
    public function index()
    {
        return MediaFolder::with('children')->whereNull('parent_id')->orderBy('order')->get();
    }
    public function store(StoreFolderRequest $req)
    {
        return MediaFolder::create($req->validated());
    }
    public function show(MediaFolder $folder)
    {
        return $folder->load('children', 'media');
    }
    public function update(StoreFolderRequest $req, MediaFolder $folder)
    {
        $folder->update($req->validated());
        return $folder;
    }
    public function destroy(MediaFolder $folder)
    {
        $folder->delete();
        return response()->json(null, 204);
    }
    public function reorder(Request $req)
    {
        foreach ($req->input('order') as $idx => $id) {
            MediaFolder::where('id', $id)->update(['order' => $idx]);
        }
        return response()->json(null, 204);
    }
}
