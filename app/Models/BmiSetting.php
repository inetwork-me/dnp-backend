<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'bmi_range_from',
        'bmi_range_to',
        'classification',
        'tips',
        'recommended_water_intake',
        'order',
        'is_active'
    ];

    protected $casts = [
        'bmi_range_from' => 'decimal:2',
        'bmi_range_to' => 'decimal:2',
        'classification' => 'array',
        'tips' => 'array',
        'recommended_water_intake' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get BMI settings ordered by range
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get BMI settings ordered by range
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('bmi_range_from');
    }

    /**
     * Find the appropriate BMI setting for a given BMI value
     */
    public static function findByBmi($bmiValue)
    {
        return static::active()
            ->where('bmi_range_from', '<=', $bmiValue)
            ->where('bmi_range_to', '>', $bmiValue)
            ->first();
    }

    /**
     * Get localized classification
     */
    public function getClassification($locale = 'en')
    {
        return $this->classification[$locale] ?? $this->classification['en'] ?? '';
    }

    /**
     * Get localized tips
     */
    public function getTips($locale = 'en')
    {
        return $this->tips[$locale] ?? $this->tips['en'] ?? '';
    }

    /**
     * Get localized water intake recommendation
     */
    public function getWaterIntakeRecommendation($locale = 'en')
    {
        return $this->recommended_water_intake[$locale] ?? $this->recommended_water_intake['en'] ?? '';
    }
}
