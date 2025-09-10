<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\BmiSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApiBmiSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = BmiSetting::orderBy('order')->orderBy('bmi_range_from')->get();
        
        return response()->json([
            'data' => $settings->map(function ($setting) {
                return [
                    'id' => $setting->id,
                    'bmi_range_from' => $setting->bmi_range_from,
                    'bmi_range_to' => $setting->bmi_range_to,
                    'classification' => $setting->classification,
                    'tips' => $setting->tips,
                    'recommended_water_intake' => $setting->recommended_water_intake,
                    'order' => $setting->order,
                    'is_active' => $setting->is_active,
                    'created_at' => $setting->created_at,
                    'updated_at' => $setting->updated_at
                ];
            })
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bmi_range_from' => 'required|numeric|min:0',
            'bmi_range_to' => 'required|numeric|gt:bmi_range_from',
            'classification' => 'required|array',
            'classification.en' => 'required|string|max:255',
            'classification.ar' => 'required|string|max:255',
            'tips' => 'required|array',
            'tips.en' => 'required|string',
            'tips.ar' => 'required|string',
            'recommended_water_intake' => 'required|array',
            'recommended_water_intake.en' => 'required|string',
            'recommended_water_intake.ar' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['order'] = $validated['order'] ?? BmiSetting::max('order') + 1;

        $setting = BmiSetting::create($validated);

        return response()->json([
            'message' => 'BMI setting created successfully',
            'data' => $setting
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BmiSetting $bmiSetting)
    {
        return response()->json(['data' => $bmiSetting]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BmiSetting $bmiSetting)
    {
        $validated = $request->validate([
            'bmi_range_from' => 'required|numeric|min:0',
            'bmi_range_to' => 'required|numeric|gt:bmi_range_from',
            'classification' => 'required|array',
            'classification.en' => 'required|string|max:255',
            'classification.ar' => 'required|string|max:255',
            'tips' => 'required|array',
            'tips.en' => 'required|string',
            'tips.ar' => 'required|string',
            'recommended_water_intake' => 'required|array',
            'recommended_water_intake.en' => 'required|string',
            'recommended_water_intake.ar' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        $bmiSetting->update($validated);

        return response()->json([
            'message' => 'BMI setting updated successfully',
            'data' => $bmiSetting->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BmiSetting $bmiSetting)
    {
        $bmiSetting->delete();

        return response()->json([
            'message' => 'BMI setting deleted successfully'
        ]);
    }

    /**
     * Toggle active status of BMI setting
     */
    public function toggleStatus(BmiSetting $bmiSetting)
    {
        $bmiSetting->update([
            'is_active' => !$bmiSetting->is_active
        ]);

        return response()->json([
            'message' => 'BMI setting status updated successfully',
            'data' => $bmiSetting->fresh()
        ]);
    }

    /**
     * Bulk update order of BMI settings
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:bmi_settings,id',
            'settings.*.order' => 'required|integer|min:0'
        ]);

        foreach ($validated['settings'] as $settingData) {
            BmiSetting::where('id', $settingData['id'])
                ->update(['order' => $settingData['order']]);
        }

        return response()->json([
            'message' => 'BMI settings order updated successfully'
        ]);
    }
}
