<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CMS\Post;
use App\Models\CMS\PostTranslation;
use Illuminate\Http\Request;

class PostController extends Controller
{
    private $path;

    public function __construct()
    {
        $this->path = 'cms.posts.';
    }

    public function index()
    {
        $posts = Post::paginate(20);
        return view($this->path.'index', compact('posts'));
    }

    public function create()
    {
        return view($this->path.'create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|unique:posts,slug',
            'plural_label' => 'required',
            'singular_label' => 'required',
            'menu_name' => 'nullable',
            'description' => 'nullable',
            'supports' => 'array',
        ]);
        $data['icon'] = $request->icon;

        $post_type = Post::create($data);

        $post_type_translations = PostTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'post_type_id' => $post_type->id]);
        $post_type_translations->plural_label = $request->plural_label;
        $post_type_translations->singular_label = $request->singular_label;
        $post_type_translations->menu_name = $request->menu_name;
        $post_type_translations->description = $request->description;

        $post_type_translations->save();

        flash(translate('Post Type created.'))->success();
        return redirect()->route('cms.posts.index');
    }

    public function show(Post $id)
    {
        return view($this->path.'show', compact('postType'));
    }

    public function edit($id,Request $request)
    {
        $post = Post::find($id);
        $lang   = $request->lang;
        return view($this->path.'edit', compact('post','lang'));
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $data = $request->validate([
            'slug' => 'required|unique:posts,slug,' . $post->id,
            'plural_label' => 'required',
            'singular_label' => 'required',
            'menu_name' => 'nullable',
            'description' => 'nullable',
            'supports' => 'array',
        ]);
        
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $post->plural_label = $request->plural_label;
            $post->singular_label = $request->singular_label;
            $post->menu_name = $request->menu_name;
            $post->description = $request->description;
        }

        $post->icon = $request->icon;
        $post->save();

        $post_translations = PostTranslation::firstOrNew(['lang' => $request->lang, 'post_id' => $post->id]);
        $post_translations->plural_label = $request->plural_label;
        $post_translations->singular_label = $request->singular_label;
        $post_translations->menu_name = $request->menu_name;
        $post_translations->description = $request->description;

        $post_translations->save();

        flash(translate('Post Type updated.'))->success();
        return redirect()->route('cms.posts.index');
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        $post->delete();
        flash(translate('Post Type deleted.'))->success();
        return redirect()->route('cms.posts.index');
    }
}
