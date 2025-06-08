<?php

// app/Http/Controllers/ApiPostTypeCategoryController.php
namespace App\Http\Controllers;

use App\Models\PostType;

class ApiPostTypeCategoryController extends Controller
{
    public function index(PostType $postType) {
        return $postType->categories()->get();
    }
}
