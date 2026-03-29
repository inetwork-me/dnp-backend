<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Menu;
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

            if ($group === 'menu') {

                $menuId = $value['default'];
                $menu = Menu::where('id', '=', $menuId)->get();

                $grouped[$group][$innerKey] = $menu[0]->items;
            } else {
                $grouped[$group][$innerKey] = $value;
            }
            // Use innerKey without prefix
        }

        return response()->json($grouped);
    }
}
