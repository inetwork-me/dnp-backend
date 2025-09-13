<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BmiRecord;
use App\Models\BmiSetting;

class WebsiteBmi extends Controller
{
    public function index()
    {
        return BmiRecord::latest()->paginate(15);
    }

    public function show(BmiRecord $bmi)
    {
        \Log::info('WebsiteBmi::show called for BMI ID: ' . $bmi->id);
        
        // Get current BMI category for this specific record
        $currentBmiSetting = BmiSetting::findByBmi($bmi->bmi);
        
        // Get all BMI settings for the frontend
        $allBmiSettings = BmiSetting::active()->ordered()->get();
        
        // Debug: Log the count of BMI settings
        \Log::info('BMI Settings Count: ' . $allBmiSettings->count());
        \Log::info('First BMI Setting: ' . json_encode($allBmiSettings->first() ? $allBmiSettings->first()->toArray() : null));
        
        $response = $bmi->toArray();
        
        // Add current BMI details
        if ($currentBmiSetting) {
            // Find the same setting from the allBmiSettings collection which is working correctly
            $matchingSetting = $allBmiSettings->where('id', $currentBmiSetting->id)->first();
            
              $response['bmi_details'] = [
                    'classification' => $matchingSetting->classification,
                    'tips' => $matchingSetting->tips,
                    'recommended_water_intake' => $matchingSetting->recommended_water_intake,
                    'range_from' => $matchingSetting->bmi_range_from,
                    'range_to' => $matchingSetting->bmi_range_to
              ];
        }
        
        // Add all BMI settings for dynamic classification on frontend
        $response['bmi_settings'] = $allBmiSettings->map(function ($setting) {
            return [
                'id' => $setting->id,
                'bmi_range_from' => $setting->bmi_range_from,
                'bmi_range_to' => $setting->bmi_range_to,
                'classification' => $setting->classification,
                'tips' => $setting->tips,
                'recommended_water_intake' => $setting->recommended_water_intake,
                'order' => $setting->order,
                'is_active' => $setting->is_active
            ];
        });
        
        // Debug: Add a simple test field
        $response['bmi_settings_debug'] = 'BMI settings loaded: ' . $allBmiSettings->count();
        
        // Force add a test field to confirm this method is being called
        $response['test_method_called'] = 'WebsiteBmi::show was executed';
        
        return response()->json($response);
    }

    public function store(Request $request)
    {
        $data = $this->validateInput($request);
        $metrics = $this->calculateMetrics($data);

        $record = BmiRecord::create(array_merge($data, $metrics));
        
        // Add BMI details using settings
        $bmiSetting = BmiSetting::findByBmi($record->bmi);
        
        // Get all BMI settings for the frontend
        $allBmiSettings = BmiSetting::active()->ordered()->get();
        
        $response = $record->toArray();
        
        if ($bmiSetting) {
            $response['bmi_details'] = [
                'classification' => $bmiSetting->classification,
                'tips' => $bmiSetting->tips,
                'recommended_water_intake' => $bmiSetting->recommended_water_intake,
                'range_from' => $bmiSetting->bmi_range_from,
                'range_to' => $bmiSetting->bmi_range_to
            ];
        }
        
        // Add all BMI settings for dynamic classification on frontend
        $response['bmi_settings'] = $allBmiSettings->map(function ($setting) {
            return [
                'id' => $setting->id,
                'bmi_range_from' => $setting->bmi_range_from,
                'bmi_range_to' => $setting->bmi_range_to,
                'classification' => $setting->classification,
                'tips' => $setting->tips,
                'recommended_water_intake' => $setting->recommended_water_intake,
                'order' => $setting->order,
                'is_active' => $setting->is_active
            ];
        });

        return response()->json($response, 201);
    }

    public function update(Request $request, BmiRecord $bmiRecord)
    {
        $data = $this->validateInput($request);
        $metrics = $this->calculateMetrics($data);

        $bmiRecord->update(array_merge($data, $metrics));

        return response()->json($bmiRecord);
    }

    public function destroy(BmiRecord $bmiRecord)
    {
        $bmiRecord->delete();

        return response()->noContent();
    }

    protected function validateInput(Request $request)
    {
        return $request->validate([
            'gender'   => 'required|in:male,female',
            'age'      => 'required|integer|min:0',
            'weight'   => 'required|numeric|min:0',
            'height'   => 'required|numeric|min:0',
            'activity' => 'required|in:sedentary,light,moderate,active,very_active',
        ]);
    }

    protected function calculateMetrics(array $data): array
    {
        $heightM = $data['height'] / 100;
        $bmi     = round($data['weight'] / ($heightM * $heightM), 1);

        // Get BMI category from settings instead of hardcoded values
        $bmiSetting = BmiSetting::findByBmi($bmi);
        $bmiCategory = $bmiSetting ? ($bmiSetting->classification['en'] ?? 'unknown') : 'unknown';

        // Convert to lowercase and replace spaces with underscores for backward compatibility
        $bmiCategoryKey = strtolower(str_replace([' ', '-'], '_', $bmiCategory));

        if ($data['gender'] === 'male') {
            $bmr = 10 * $data['weight'] + 6.25 * $data['height'] - 5 * $data['age'] + 5;
        } else {
            $bmr = 10 * $data['weight'] + 6.25 * $data['height'] - 5 * $data['age'] - 161;
        }
        $bmr = round($bmr);

        $factors = ['sedentary' => 1.2, 'light' => 1.375, 'moderate' => 1.55, 'active' => 1.725, 'very_active' => 1.9];
        $tee = round($bmr * $factors[$data['activity']]);
        $water = round($data['weight'] * 0.03, 2);

        return [
            'bmi'            => $bmi,
            'bmi_category'   => $bmiCategoryKey,
            'bmr'            => $bmr,
            'tee'            => $tee,
            'calories'       => $tee,
            'water_intake_l' => $water,
        ];
    }

    /**
     * Get all BMI settings for frontend display
     */
    public function settings()
    {
        $settings = BmiSetting::active()->ordered()->get();

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
                    'is_active' => $setting->is_active
                ];
            })
        ]);
    }

    public function showWithSettings(BmiRecord $bmi)
    {
        // Get current BMI category for this specific record
        $currentBmiSetting = BmiSetting::findByBmi($bmi->bmi);
        
        // Get all BMI settings for the frontend
        $allBmiSettings = BmiSetting::active()->ordered()->get();
        
        $response = $bmi->toArray();
        
        // Add current BMI details
        if ($currentBmiSetting) {
            // Find the same setting from the allBmiSettings collection which is working correctly
            $matchingSetting = $allBmiSettings->where('id', $currentBmiSetting->id)->first();
            
            if ($matchingSetting) {
                $response['bmi_details'] = [
                    'classification' => $matchingSetting->classification,
                    'tips' => $matchingSetting->tips,
                    'recommended_water_intake' => $matchingSetting->recommended_water_intake,
                    'range_from' => $matchingSetting->bmi_range_from,
                    'range_to' => $matchingSetting->bmi_range_to
                ];
            } else {
                // Fallback to the original method with explicit JSON decoding
                $response['bmi_details'] = [
                    'classification' => json_decode($currentBmiSetting->classification, true) ?: $currentBmiSetting->classification,
                    'tips' => json_decode($currentBmiSetting->tips, true) ?: $currentBmiSetting->tips,
                    'recommended_water_intake' => json_decode($currentBmiSetting->recommended_water_intake, true) ?: $currentBmiSetting->recommended_water_intake,
                    'range_from' => $currentBmiSetting->bmi_range_from,
                    'range_to' => $currentBmiSetting->bmi_range_to
                ];
            }
        }
        
        // Add all BMI settings for dynamic classification on frontend
        $response['bmi_settings'] = $allBmiSettings->map(function ($setting) {
            return [
                'id' => $setting->id,
                'bmi_range_from' => $setting->bmi_range_from,
                'bmi_range_to' => $setting->bmi_range_to,
                'classification' => $setting->classification,
                'tips' => $setting->tips,
                'recommended_water_intake' => $setting->recommended_water_intake,
                'order' => $setting->order,
                'is_active' => $setting->is_active
            ];
        });
        
        return response()->json($response);
    }
}
