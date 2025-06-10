<?php



namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\PostType;

class ApiPostTypeCategoryController extends Controller
{
    public function index(PostType $postType)
    {
        return $postType->categories()->get();
    }
}
