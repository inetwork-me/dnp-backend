<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WebsitePostsController extends Controller
{
    public function index(Request $request)
    {
        $pt = PostType::where('slug', $request->query('post_type'))->firstOrFail();

        return Post::where('post_type_id', $pt->id)
            ->with('postType')
            ->orderBy('created_at', 'desc')
            ->get();
    }



    public function show(Request $request)
    {
        $post = Post::where('slug', $request->slug);
        return $post->get();
    }



    public function destroy(Post $post)
    {
        $post->delete();
        return response()->noContent();
    }
}
