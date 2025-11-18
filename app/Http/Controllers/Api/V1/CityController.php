<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    /**
     * Get all active Aramex cities with translations
     */
    public function index(Request $request)
    {
        $locale = $request->header('Accept-Language', 'en');

        // Default to Egypt if no country_code is provided (backward compatibility)
        $countryCode = $request->input('country_code', 'EG');

        $query = DB::table('aramex_cities')
            ->where('is_active', true)
            ->where('country_code', $countryCode);

        // Optional search parameter
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $cities = $query->orderBy('name', 'asc')->get(['id', 'aramex_city_id', 'name', 'name_ar']);

        $data = $cities->map(function ($city) use ($locale) {
            return [
                'id' => $city->id,
                'name' => $city->name, // English value for Aramex
                'label' => $locale === 'ar' && $city->name_ar ? $city->name_ar : $city->name,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get city by ID
     */
    public function show($id)
    {
        $city = DB::table('aramex_cities')
            ->where('id', $id)
            ->where('is_active', true)
            ->first(['id', 'aramex_city_id', 'name', 'name_ar']);

        if (!$city) {
            return response()->json([
                'success' => false,
                'message' => 'City not found'
            ], 404);
        }

        $locale = request()->header('Accept-Language', 'en');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $city->id,
                'name' => $city->name,
                'label' => $locale === 'ar' && $city->name_ar ? $city->name_ar : $city->name,
            ]
        ]);
    }
}
