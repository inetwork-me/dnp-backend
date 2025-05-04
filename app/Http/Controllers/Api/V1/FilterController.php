<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BrandCollection;
use App\Http\Resources\V1\CategoryCollection;
use App\Models\Brand;
use App\Models\Category;
use Cache;

class FilterController extends Controller
{
    public function categories()
    {
        return new CategoryCollection(Category::where('parent_id', 0)->get());
    }

    public function brands()
    {
        return new BrandCollection(Brand::where('top', 1)->limit(20)->get());
    }


}
