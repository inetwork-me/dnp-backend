<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RecipeCategory;
use App\Models\RecipeCategoryTranslation;

class RecipeCategoryController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_blog_categories'])->only('index');
        $this->middleware(['permission:add_blog_category'])->only('create');
        $this->middleware(['permission:edit_blog_category'])->only('edit');
        $this->middleware(['permission:delete_blog_category'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $categories = RecipeCategory::orderBy('category_name', 'asc');

        if ($request->has('search')) {
            $sort_search = $request->search;
            $categories = $categories->where('category_name', 'like', '%' . $sort_search . '%');
        }

        $categories = $categories->paginate(15);
        return view('backend.recipes.category.index', compact('categories', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $all_categories = RecipeCategory::all();
        return view('backend.recipes.category.create', compact('all_categories'));
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
            'category_name' => 'required|max:255',
        ]);

        $category = new RecipeCategory;

        $category->category_name = $request->category_name;
        $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->category_name));

        $category->save();


        flash(translate('Recipe category has been created successfully'))->success();
        return redirect()->route('recipe-category.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
     {
         $lang  = $request->lang;
        $cateogry = RecipeCategory::find($id);
        $all_categories = RecipeCategory::all();

        return view('backend.recipes.category.edit',  compact('cateogry', 'all_categories','lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $recipe_category)
    {
        $request->validate([
            'category_name' => 'required|max:255',
        ]);

        $category = RecipeCategory::find($recipe_category);

        if($request->lang == env("DEFAULT_LANGUAGE")){
            $category->category_name = $request->category_name;
            $category->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->category_name));
        }

        $category->save();
        

        $recipe_category_translation = RecipeCategoryTranslation::firstOrNew(['lang' => $request->lang, 'recipe_category_id' => $category->id]);
        $recipe_category_translation->category_name = $request->category_name;
        $recipe_category_translation->save();


        flash(translate('Recipe category has been updated successfully'))->success();
        return redirect()->route('recipe-category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        RecipeCategory::find($id)->delete();
        flash(translate('Recipe category has been deleted successfully'))->success();
        return redirect()->route('recipe-category.index');
    }
}
