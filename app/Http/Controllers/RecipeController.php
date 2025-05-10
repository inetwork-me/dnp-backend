<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RecipeCategory;
use App\Models\Recipe;
use App\Models\RecipeTranslation;

class RecipeController extends Controller
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
        $recipes = Recipe::orderBy('created_at', 'desc');
        
        if ($request->search != null){
            $recipes = $recipes->where('title', 'like', '%'.$request->search.'%');
            $sort_search = $request->search;
        }

        $recipes = $recipes->paginate(15);

        return view('backend.recipes.recipes.index', compact('recipes','sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $recipe_categories = RecipeCategory::all();
        return view('backend.recipes.recipes.create', compact('recipe_categories'));
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

        $recipe = new Recipe;
        
        $recipe->category_id = $request->category_id;
        $recipe->title = $request->title;
        $recipe->calories = $request->calories;
        $recipe->time_make = $request->time_make;
        $recipe->banner = $request->banner;
        $recipe->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        $recipe->description = $request->description;
        
        $recipe->meta_title = $request->meta_title;
        $recipe->meta_img = $request->meta_img;
        $recipe->meta_description = $request->meta_description;
        $recipe->meta_keywords = $request->meta_keywords;
        
        $recipe->save();

        $recipe_translation = RecipeTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'recipe_id' => $recipe->id]);
        $recipe_translation->title = $request->title;
        $recipe_translation->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        $recipe_translation->description = $request->description;
        
        $recipe_translation->meta_title = $request->meta_title;
        $recipe_translation->meta_img = $request->meta_img;
        $recipe_translation->meta_description = $request->meta_description;
        $recipe_translation->meta_keywords = $request->meta_keywords;

        $recipe_translation->save();

        flash(translate('Recipe has been created successfully'))->success();
        return redirect()->route('recipe.index');
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
        $recipe = Recipe::find($id);
        $recipe_categories = RecipeCategory::all();
        $lang   = $request->lang;
        return view('backend.recipes.recipes.edit', compact('recipe','recipe_categories','lang'));
    }
    
    public function update(Request $request, $id)
    {        
        $request->validate([
            'category_id' => 'required',
            'title' => 'required|max:255',
        ]);

        $recipe = Recipe::find($id);

        if($request->lang == env("DEFAULT_LANGUAGE")){
            $recipe->title = $request->title;
            if ($request->slug != null) {
                $recipe->slug = strtolower($request->slug);
            }
            else {
                $recipe->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
            }
            $recipe->description = $request->description;
            
            $recipe->meta_title = $request->meta_title;
            $recipe->meta_img = $request->meta_img;
            $recipe->meta_description = $request->meta_description;
            $recipe->meta_keywords = $request->meta_keywords;
        }

        $recipe->category_id = $request->category_id;
        $recipe->banner = $request->banner;
        $recipe->time_make = $request->time_make;
        $recipe->calories = $request->calories;
                
        $recipe->save();
        

        

        $recipe_translation = RecipeTranslation::firstOrNew(['lang' => $request->lang, 'recipe_id' => $recipe->id]);
        $recipe_translation->title = $request->title;
        $recipe_translation->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        $recipe_translation->description = $request->description;
        
        $recipe_translation->meta_title = $request->meta_title;
        $recipe_translation->meta_img = $request->meta_img;
        $recipe_translation->meta_description = $request->meta_description;
        $recipe_translation->meta_keywords = $request->meta_keywords;
        $recipe_translation->save();

        flash(translate('Recipe has been updated successfully'))->success();
        return redirect()->route('recipe.index');
    }
    
    public function change_status(Request $request) {
        $recipe = Recipe::find($request->id);
        $recipe->status = $request->status;
        
        $recipe->save();
        return 1;
    }
    
    public function destroy($id)
    {
        Recipe::find($id)->delete();
        
        flash(translate('Recipe has been deleted successfully'))->success();
        return redirect()->route('recipe.index');
    }


    public function all_recipe(Request $request) {
        $selected_categories = array();
        $search = null;
        $recipes = Recipe::query();

        if ($request->has('search')) {
            $search = $request->search;;
            $recipes->where(function ($q) use ($search) {
                foreach (explode(' ', trim($search)) as $word) {
                    $q->where('title', 'like', '%' . $word . '%')
                        ->orWhere('short_description', 'like', '%' . $word . '%');
                }
            });

            $case1 = $search . '%';
            $case2 = '%' . $search . '%';

            $recipes->orderByRaw("CASE 
                WHEN title LIKE '$case1' THEN 1 
                WHEN title LIKE '$case2' THEN 2 
                ELSE 3 
                END");
        }
        
        if ($request->has('selected_categories')) {
            $selected_categories = $request->selected_categories;
            $recipe_categories = RecipeCategory::whereIn('slug', $selected_categories)->pluck('id')->toArray();

            $recipes->whereIn('category_id', $recipe_categories);
        }
        
        $recipes = $recipes->where('status', 1)->orderBy('created_at', 'desc')->paginate(12);

        $recent_recipes = Recipe::where('status', 1)->orderBy('created_at', 'desc')->limit(9)->get();

        return view("frontend.recipe.listing", compact('recipes', 'selected_categories', 'search', 'recent_recipes'));
    }
    
    public function recipe_details($slug) {
        $recipe = Recipe::where('slug', $slug)->first();
        $recent_recipes = Recipe::where('status', 1)->orderBy('created_at', 'desc')->limit(9)->get();
        return view("frontend.recipe.details", compact('recipe', 'recent_recipes'));
    }
}
