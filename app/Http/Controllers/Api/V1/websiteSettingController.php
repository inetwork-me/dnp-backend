<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class WebsiteSettingController extends Controller
{
    /**
     * Return settings grouped by prefix, stripping the prefix from inner keys.
     */
    public function index()
    {
        // Fetch all settings as key => value
        $all = Setting::all()->pluck('value', 'key')->toArray();

        $grouped = [];

        foreach ($all as $key => $value) {
            if (strpos($key, '_') === false) {
                $group = 'other';
                $innerKey = $key;
            } else {
                list($group, $innerKey) = explode('_', $key, 2);
            }

            // Initialize group if needed
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }

            // Use innerKey without prefix
            $grouped[$group][$innerKey] = $value;
        }

        return response()->json($grouped);
    }
}
