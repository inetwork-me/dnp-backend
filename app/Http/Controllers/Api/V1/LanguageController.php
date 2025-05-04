<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Http\Resources\V1\LanguageCollection;
use Cache;

class LanguageController extends Controller
{
    public function getList(Request $request)
    {
        return new LanguageCollection(Language::where('status', 1)->get());
    }
}
