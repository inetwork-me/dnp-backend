<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use App\Models\Blog;
use App\Models\BlogTranslation;

class BlogController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_blogs'])->only('index');
        $this->middleware(['permission:add_blog'])->only('create');
        $this->middleware(['permission:edit_blog'])->only('edit');
        $this->middleware(['permission:delete_blog'])->only('destroy');
        $this->middleware(['permission:publish_blog'])->only('change_status');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $blogs = Blog::orderBy('created_at', 'desc');
        
        if ($request->search != null){
            $blogs = $blogs->where('title', 'like', '%'.$request->search.'%');
            $sort_search = $request->search;
        }

        $blogs = $blogs->paginate(15);

        return view('backend.blog_system.blog.index', compact('blogs','sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $blog_categories = BlogCategory::all();
        return view('backend.blog_system.blog.create', compact('blog_categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'category_id' => 'required',
            'title' => 'required|max:255',
        ]);

        $blog = new Blog;
        
        $blog->category_id = $request->category_id;
        $blog->title = $request->title;
        $blog->banner = $request->banner;
        $blog->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        $blog->short_description = $request->short_description;
        $blog->description = $request->description;
        
        $blog->meta_title = $request->meta_title;
        $blog->meta_img = $request->meta_img;
        $blog->meta_description = $request->meta_description;
        $blog->meta_keywords = $request->meta_keywords;
        
        $blog->save();

        $blog_translation = BlogTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'blog_id' => $blog->id]);
        $blog_translation->title = $request->title;
        $blog_translation->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        $blog_translation->short_description = $request->short_description;
        $blog_translation->description = $request->description;
        
        $blog_translation->meta_title = $request->meta_title;
        $blog_translation->meta_img = $request->meta_img;
        $blog_translation->meta_description = $request->meta_description;
        $blog_translation->meta_keywords = $request->meta_keywords;

        $blog_translation->save();

        flash(translate('Blog post has been created successfully'))->success();
        return redirect()->route('blog.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $blog = Blog::find($id);
        $blog_categories = BlogCategory::all();
        $lang   = $request->lang;
        return view('backend.blog_system.blog.edit', compact('blog','blog_categories','lang'));
    }
    
    public function update(Request $request, $id)
    {        
        $request->validate([
            'category_id' => 'required',
            'title' => 'required|max:255',
        ]);

        $blog = Blog::find($id);
        // dd($request->all());

        if($request->lang == env("DEFAULT_LANGUAGE")){
            $blog->title = $request->title;
            if ($request->slug != null) {
                $blog->slug = strtolower($request->slug);
            }
            else {
                $blog->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
            }
            $blog->short_description = $request->short_description;
            $blog->description = $request->description;
            
            $blog->meta_title = $request->meta_title;
            $blog->meta_img = $request->meta_img;
            $blog->meta_description = $request->meta_description;
            $blog->meta_keywords = $request->meta_keywords;
        }

        $blog->category_id = $request->category_id;
        $blog->banner = $request->banner;
                
        $blog->save();
        

        

        $blog_translation = BlogTranslation::firstOrNew(['lang' => $request->lang, 'blog_id' => $blog->id]);
        $blog_translation->title = $request->title;
        $blog_translation->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        $blog_translation->short_description = $request->short_description;
        $blog_translation->description = $request->description;
        
        $blog_translation->meta_title = $request->meta_title;
        $blog_translation->meta_img = $request->meta_img;
        $blog_translation->meta_description = $request->meta_description;
        $blog_translation->meta_keywords = $request->meta_keywords;
        $blog_translation->save();

        flash(translate('Blog post has been updated successfully'))->success();
        return redirect()->route('blog.index');
    }
    
    public function change_status(Request $request) {
        $blog = Blog::find($request->id);
        $blog->status = $request->status;
        
        $blog->save();
        return 1;
    }
    
    public function destroy($id)
    {
        Blog::find($id)->delete();
        
        flash(translate('Blog post has been deleted successfully'))->success();
        return redirect()->route('blog.index');
    }


    public function all_blog(Request $request) {
        $selected_categories = array();
        $search = null;
        $blogs = Blog::query();

        if ($request->has('search')) {
            $search = $request->search;;
            $blogs->where(function ($q) use ($search) {
                foreach (explode(' ', trim($search)) as $word) {
                    $q->where('title', 'like', '%' . $word . '%')
                        ->orWhere('short_description', 'like', '%' . $word . '%');
                }
            });

            $case1 = $search . '%';
            $case2 = '%' . $search . '%';

            $blogs->orderByRaw("CASE 
                WHEN title LIKE '$case1' THEN 1 
                WHEN title LIKE '$case2' THEN 2 
                ELSE 3 
                END");
        }
        
        if ($request->has('selected_categories')) {
            $selected_categories = $request->selected_categories;
            $blog_categories = BlogCategory::whereIn('slug', $selected_categories)->pluck('id')->toArray();

            $blogs->whereIn('category_id', $blog_categories);
        }
        
        $blogs = $blogs->where('status', 1)->orderBy('created_at', 'desc')->paginate(12);

        $recent_blogs = Blog::where('status', 1)->orderBy('created_at', 'desc')->limit(9)->get();

        return view("frontend.blog.listing", compact('blogs', 'selected_categories', 'search', 'recent_blogs'));
    }
    
    public function blog_details($slug) {
        $blog = Blog::where('slug', $slug)->first();
        $recent_blogs = Blog::where('status', 1)->orderBy('created_at', 'desc')->limit(9)->get();
        return view("frontend.blog.details", compact('blog', 'recent_blogs'));
    }
}
