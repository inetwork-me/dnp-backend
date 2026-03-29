<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key', 'value', 'type', 'description', 'group'
    ];

    // Helper method to get typed value
    public function getTypedValue()
    {
        return match($this->type) {
            'integer' => (int) $this->value,
            'boolean' => (bool) $this->value,
            'decimal' => (float) $this->value,
            'json' => json_decode($this->value, true),
            default => $this->value
        };
    }

    // Static method to get setting value
    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->getTypedValue() : $default;
    }

    // Static method to set setting value
    public static function set($key, $value, $type = 'string', $description = null, $group = 'general')
    {
        $valueToStore = match($type) {
            'json' => json_encode($value),
            default => (string) $value
        };

        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $valueToStore,
                'type' => $type,
                'description' => $description,
                'group' => $group
            ]
        );
    }

    // Get all settings as array
    public static function getAllAsArray()
    {
        return static::all()->mapWithKeys(function ($setting) {
            return [$setting->key => $setting->getTypedValue()];
        })->toArray();
    }

    // Get settings by group
    public static function getByGroup($group)
    {
        return static::where('group', $group)
                    ->get()
                    ->mapWithKeys(function ($setting) {
                        return [$setting->key => $setting->getTypedValue()];
                    })->toArray();
    }
}
