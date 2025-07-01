<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BmiRecord;

class BmiController extends Controller
{
    public function index()
    {
        return BmiRecord::latest()->paginate(15);
    }

    public function show(BmiRecord $bmiRecord)
    {
        return $bmiRecord;
    }

    public function store(Request $request)
    {
        $data = $this->validateInput($request);
        $metrics = $this->calculateMetrics($data);

        $record = BmiRecord::create(array_merge($data, $metrics));

        return response()->json($record, 201);
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

        $categories = [
            ['key' => 'underweight', 'min' => 0, 'max' => 18.5],
            ['key' => 'normal', 'min' => 18.5, 'max' => 24.9],
            ['key' => 'overweight', 'min' => 25, 'max' => 29.9],
            ['key' => 'obese', 'min' => 30, 'max' => 34.9],
            ['key' => 'extremely_obese', 'min' => 35, 'max' => 999],
        ];
        $bmiCategory = collect($categories)
            ->first(fn ($c) => $bmi >= $c['min'] && $bmi < $c['max']);

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
            'bmi_category'   => $bmiCategory['key'],
            'bmr'            => $bmr,
            'tee'            => $tee,
            'calories'       => $tee,
            'water_intake_l' => $water,
        ];
    }
}
