<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApiPostsController extends Controller
{
    public function index(Request $request)
    {
        $pt = PostType::where('slug', $request->query('post_type'))->firstOrFail();

        return Post::where('post_type_id', $pt->id)
            ->with('postType')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function store(Request $request)
    {
        $payload = $request->validate([
            'post_type'      => ['required', 'exists:post_types,slug'],
            'title'          => ['required', 'array'],
            'title.en'       => ['required', 'string'],
            'title.ar'       => ['required', 'string'],
            'slug'           => ['required', 'alpha_dash', 'max:255'],
            'content'        => ['nullable', 'array'],
            'blocks'        => ['nullable', 'array'],
            'featured_image' => ['nullable', 'url'],
            'status'         => ['in:draft,published'],
            'published_at'   => ['nullable', 'date'],
        ]);

        $pt = PostType::where('slug', $payload['post_type'])->first();

        $post = Post::create([
            'post_type_id'   => $pt->id,
            'title'          => $payload['title'],
            'slug'           => $payload['slug'],
            'content'        => $payload['content'] ?? [],
            'blocks'        => $payload['blocks'] ?? [],
            'featured_image' => $payload['featured_image'] ?? null,
            'status'         => $payload['status'] ?? 'draft',
            'published_at'   => $payload['published_at'] ?? null,
            'author_id'      => auth()->id(),
        ]);

        return response()->json($post, 201);
    }

    public function show(Post $post)
    {
        return $post->load('postType');
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title'        => ['required', 'array'],
            'title.en'     => ['required', 'string'],
            'title.ar'     => ['required', 'string'],
            'slug'         => [
                'required', 'alpha_dash', 'max:255',
                Rule::unique('posts', 'slug')
                    ->where(fn ($q) => $q->where('post_type_id', $post->post_type_id))
                    ->ignore($post->id)
            ],
            'content'      => ['nullable', 'array'],
            'blocks'      => ['nullable', 'array'],
            'featured_image' => ['nullable', 'url'],
            'status'       => ['in:draft,published'],
            'published_at' => ['nullable', 'date'],
        ]);

        $post->update($data);
        return $post;
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return response()->noContent();
    }
}
