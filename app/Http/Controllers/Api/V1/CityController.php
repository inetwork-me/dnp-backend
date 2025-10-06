<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    /**
     * Get all active Aramex cities
     */
    public function index(Request $request)
    {
        $query = DB::table('aramex_cities')
            ->where('is_active', true)
            ->where('country_code', 'EG');

        // Optional search parameter
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $cities = $query->orderBy('name', 'asc')->get(['id', 'aramex_city_id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $cities
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
            ->first(['id', 'aramex_city_id', 'name']);

        if (!$city) {
            return response()->json([
                'success' => false,
                'message' => 'City not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $city
        ]);
    }
}
