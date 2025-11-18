<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Get all active countries with translations
     */
    public function index(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');

        $countries = Country::where('status', 1)
            ->orderBy('name', 'asc')
            ->get(['id', 'code', 'name', 'label']);

        $data = $countries->map(function ($country) use ($locale) {
            return [
                'id' => $country->id,
                'code' => $country->code,
                'name' => $country->name,
                'label' => $country->getTranslatedLabel($locale),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get country by ID
     */
    public function show($id)
    {
        $locale = request()->header('Accept-Language', 'en');

        $country = Country::where('id', $id)
            ->where('status', 1)
            ->first(['id', 'code', 'name', 'label']);

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $country->id,
                'code' => $country->code,
                'name' => $country->name,
                'label' => $country->getTranslatedLabel($locale),
            ]
        ]);
    }
}
