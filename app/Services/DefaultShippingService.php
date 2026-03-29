<?php

namespace App\Services;

use App\Models\ShippingCarrier;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use App\Models\ShippingRate;

class DefaultShippingService
{
    /**
     * Set up default shipping configuration
     */
    public static function setupDefaults(): array
    {
        $results = [];
        
        // Get Aramex carrier
        $aramexCarrier = ShippingCarrier::where('slug', 'aramex')->first();
        
        if (!$aramexCarrier) {
            $results[] = 'Aramex carrier not found';
            return $results;
        }

        // Ensure Aramex is active and supports what we need
        $aramexCarrier->update([
            'is_active' => true,
            'supports_live_rates' => true,
            'supports_tracking' => true,
            'supports_labels' => true
        ]);
        $results[] = 'Updated Aramex carrier settings';

        // Get or create default zone (worldwide)
        $defaultZone = ShippingZone::firstOrCreate(
            ['name' => 'Worldwide'],
            [
                'countries' => ['*'], // Accept all countries
                'is_active' => true
            ]
        );
        $results[] = 'Ensured worldwide shipping zone exists';

        // Create default shipping methods if they don't exist
        $methods = [
            [
                'name' => 'Aramex Standard',
                'service_code' => 'STD',
                'rate_source' => 'fixed',
                'estimated_days_min' => 3,
                'estimated_days_max' => 7,
                'is_default' => true
            ],
            [
                'name' => 'Aramex Express',
                'service_code' => 'EXP',
                'rate_source' => 'fixed', 
                'estimated_days_min' => 1,
                'estimated_days_max' => 3,
                'is_default' => false
            ]
        ];

        foreach ($methods as $methodData) {
            $method = ShippingMethod::firstOrCreate(
                [
                    'carrier_id' => $aramexCarrier->id,
                    'zone_id' => $defaultZone->id,
                    'service_code' => $methodData['service_code']
                ],
                [
                    'name' => $methodData['name'],
                    'rate_source' => $methodData['rate_source'],
                    'estimated_days_min' => $methodData['estimated_days_min'],
                    'estimated_days_max' => $methodData['estimated_days_max'],
                    'is_active' => true,
                    'sort_order' => $methodData['is_default'] ? 0 : 1
                ]
            );

            // Create rates for fixed methods
            if ($method->rate_source === 'fixed') {
                ShippingRate::firstOrCreate(
                    ['method_id' => $method->id],
                    [
                        'price_type' => 'fixed',
                        'base_price' => $methodData['service_code'] === 'STD' ? 15.00 : 25.00,
                        'free_shipping_threshold' => 100.00
                    ]
                );
            }

            $results[] = "Ensured {$methodData['name']} method exists";
        }

        // Set Aramex Standard as the primary default
        self::setDefaultShippingMethod($aramexCarrier->id, 'STD');
        $results[] = 'Set Aramex Standard as default shipping method';

        return $results;
    }

    /**
     * Set a specific method as default
     */
    public static function setDefaultShippingMethod(int $carrierId, string $serviceCode): bool
    {
        // Reset all methods to not default
        ShippingMethod::where('carrier_id', $carrierId)->update(['sort_order' => 1]);
        
        // Set the specified method as default (sort_order = 0)
        return ShippingMethod::where('carrier_id', $carrierId)
            ->where('service_code', $serviceCode)
            ->update(['sort_order' => 0]) > 0;
    }

    /**
     * Get the default shipping method for an address
     */
    public static function getDefaultShippingMethod($address = null): ?ShippingMethod
    {
        // For now, just return the method with lowest sort_order from active carrier
        return ShippingMethod::whereHas('carrier', function($query) {
            $query->where('is_active', true);
        })
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->first();
    }

    /**
     * Get available shipping methods for an address
     */
    public static function getAvailableShippingMethods($address = null): \Illuminate\Database\Eloquent\Collection
    {
        $country = $address['country'] ?? '*';
        
        // Find applicable zones
        $zones = ShippingZone::active()
            ->where(function($query) use ($country) {
                $query->whereJsonContains('countries', $country)
                      ->orWhereJsonContains('countries', '*');
            })
            ->get();

        if ($zones->isEmpty()) {
            return collect();
        }

        return ShippingMethod::whereIn('zone_id', $zones->pluck('id'))
            ->whereHas('carrier', function($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->with(['carrier', 'rates'])
            ->orderBy('sort_order')
            ->get();
    }
}