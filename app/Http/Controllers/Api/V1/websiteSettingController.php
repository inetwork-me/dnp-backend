<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class websiteSettingController extends Controller
{
    /**
     * Return all settings as an object keyed by `key`.
     */
    public function index()
    {
        $all = Setting::all()->pluck('value', 'key')->toArray();
        // Example returned shape:
        // [
        //   "site_logo"       => [ "default" => "data:image/png;base64,..." ],
        //   "site_title"      => [ "en" => "My Site", "ar" => "موقعي" ],
        //   "contact_email"   => [ "default" => "hello@example.com" ],
        //   // … etc …
        // ]
        return response()->json($all);
    }
}
