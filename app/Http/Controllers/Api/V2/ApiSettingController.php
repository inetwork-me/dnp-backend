<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiSettingController extends Controller
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

    /**
     * Update or create a single setting. Request body:
     * {
     *   "key":   "site_title",
     *   "value": { "en": "My New Title", "ar": "عنوان جديد" }
     * }
     *
     * Or for non‐multilingual fields, send:
     * {
     *   "key": "contact_email",
     *   "value": { "default": "new@example.com" }
     * }
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'key'   => 'required|string',
            'value' => 'required|array',
        ]);

        Setting::updateOrCreate(
            ['key' => $validated['key']],
            ['value' => $validated['value']]
        );

        return response()->json([
            'message' => 'Setting saved successfully.',
            'key'     => $validated['key'],
            'value'   => $validated['value'],
        ]);
    }


    /**
     * PATCH /api/settings/batch
     * Update or create multiple settings in one request.
     * Request body:
     * {
     *   "settings": [
     *     { "key": "site_title", "value": { "en": "New Title", "ar": "عنوان" } },
     *     { "key": "contact_email", "value": { "default": "new@example.com" } },
     *     // … more items …
     *   ]
     * }
     */
    public function batchUpdate(Request $request)
    {
        // First, validate that "settings" is an array of objects each having key:string, value:array
        $validator = Validator::make($request->all(), [
            'settings'             => 'required|array|min:1',
            'settings.*.key'       => 'required|string',
            'settings.*.value'     => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $updated = [];
        foreach ($request->input('settings') as $item) {
            $s = Setting::updateOrCreate(
                ['key'   => $item['key']],
                ['value' => $item['value']]
            );
            $updated[] = [
                'key'   => $s->key,
                'value' => $s->value,
            ];
        }

        return response()->json([
            'message'  => 'Batch update successful.',
            'updated'  => $updated,
        ]);
    }
}
