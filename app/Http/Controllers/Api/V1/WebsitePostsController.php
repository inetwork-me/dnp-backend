<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PostCollection;
use App\Http\Resources\V1\WebsitePostResource;
use App\Models\Post;
use App\Models\PostType;
use Illuminate\Http\JsonResponse;
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

    public function indexByType(Request $request, PostType $postType): PostCollection
    {
        // 1) Items per page (default 10, max 50)
        $perPage = min($request->input('per_page', 10), 50);

        // 2) Build the query: only eager-load needed columns, order by recency
        $query = $postType
            ->posts()
            ->with(
                'postType:id,slug,label',
                'author:id,name,email'
            )
            ->latest('created_at');

        // 3) Optional: filter only published posts
        if ($request->boolean('only_published')) {
            $query->where('status', 'published')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now());
        }

        // 4) Paginate & wrap in resource collection
        return new PostCollection($query->paginate($perPage));
    }



    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->firstOrFail();

        return new WebsitePostResource($post);
    }



    public function destroy(Post $post)
    {
        $post->delete();
        return response()->noContent();
    }
}
