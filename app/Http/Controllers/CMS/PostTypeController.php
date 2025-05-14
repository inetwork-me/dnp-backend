<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CMS\PostType;
use App\Models\CMS\PostTypeTranslation;
use Illuminate\Http\Request;

class PostTypeController extends Controller
{
    private $path;

    public function __construct()
    {
        $this->path = 'cms.post_types.';
    }

    public function index()
    {
        $postTypes = PostType::paginate(20);
        return view($this->path.'index', compact('postTypes'));
    }

    public function create()
    {
        return view($this->path.'create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|unique:post_types,slug',
            'plural_label' => 'required',
            'singular_label' => 'required',
            'menu_name' => 'nullable',
            'description' => 'nullable',
            'supports' => 'array',
        ]);
        $data['icon'] = $request->icon;

        $post_type = PostType::create($data);

        $post_type_translations = PostTypeTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'post_type_id' => $post_type->id]);
        $post_type_translations->plural_label = $request->plural_label;
        $post_type_translations->singular_label = $request->singular_label;
        $post_type_translations->menu_name = $request->menu_name;
        $post_type_translations->description = $request->description;

        $post_type_translations->save();

        flash(translate('Post Type created.'))->success();
        return redirect()->route('cms.cpt.index');
    }

    public function show(PostType $id)
    {
        return view($this->path.'show', compact('postType'));
    }

    public function edit($id,Request $request)
    {
        $postType = PostType::find($id);
        $lang   = $request->lang;
        return view($this->path.'edit', compact('postType','lang'));
    }

    public function update(Request $request, $id)
    {
        $postType = PostType::find($id);
        $data = $request->validate([
            'slug' => 'required|unique:post_types,slug,' . $postType->id,
            'plural_label' => 'required',
            'singular_label' => 'required',
            'menu_name' => 'nullable',
            'description' => 'nullable',
            'supports' => 'array',
        ]);
        
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $postType->plural_label = $request->plural_label;
            $postType->singular_label = $request->singular_label;
            $postType->menu_name = $request->menu_name;
            $postType->description = $request->description;
        }

        $postType->icon = $request->icon;
        $postType->save();

        $post_type_translations = PostTypeTranslation::firstOrNew(['lang' => $request->lang, 'post_type_id' => $postType->id]);
        $post_type_translations->plural_label = $request->plural_label;
        $post_type_translations->singular_label = $request->singular_label;
        $post_type_translations->menu_name = $request->menu_name;
        $post_type_translations->description = $request->description;

        $post_type_translations->save();

        flash(translate('Post Type updated.'))->success();
        return redirect()->route('cms.cpt.index');
    }

    public function destroy($id)
    {
        $postType = PostType::find($id);
        $postType->delete();
        flash(translate('Post Type deleted.'))->success();
        return redirect()->route('cms.cpt.index');
    }
}
