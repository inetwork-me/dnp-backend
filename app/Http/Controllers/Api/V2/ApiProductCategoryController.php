<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\CategoryCollection;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class ApiProductCategoryController extends Controller
{

    public function index(Request $request)
    {

        $parent_id = $request->query('parent_id');

        if ($parent_id) {
            $category = Category::where('slug', $parent_id)->first();
            $parent_id = $category->id;
        }

        $perPage = $request->query('per_page', 20);
        $categories = Category::where('parent_id', $parent_id)->whereDigital(0)->paginate($perPage);

        return new CategoryCollection($categories);
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
        } else {
            return new CategoryCollection(Category::where('id', 0)->get());
        }
    }
}
