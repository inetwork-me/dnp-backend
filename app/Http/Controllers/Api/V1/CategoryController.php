<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryCollection;
use App\Models\BusinessSetting;
use App\Models\Category;
use Cache;

class CategoryController extends Controller
{

    public function index($parent_id = 0)
    {
        if (request()->has('parent_id') && request()->parent_id) {
            $category = Category::where('slug', request()->parent_id)->first();
            $parent_id = $category->id;
        }

        return new CategoryCollection(Category::where('parent_id', $parent_id)->whereDigital(0)->get());
    }

    public function info($slug)
    {
        return new CategoryCollection(Category::where('slug', $slug)->get());
    }

    public function featured()
    {
        return new CategoryCollection(Category::where('featured', 1)->get());
    }

    public function home()
    {
        return new CategoryCollection(Category::whereIn('id', json_decode(get_setting('home_categories')))->get());
    }

    public function top()
    {
        if (get_setting('home_categories') != null) {
            return new CategoryCollection(Category::whereIn('id', json_decode(get_setting('home_categories')))->limit(20)->get());
        }else{
            return new CategoryCollection(Category::where('id', 0)->get());
        }
    }
}
