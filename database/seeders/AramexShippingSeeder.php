<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShippingCarrier;
use App\Models\ShippingZone;
use App\Models\ShippingMethod;
use App\Models\ShippingRate;

class AramexShippingSeeder extends Seeder
{
    /**
     * Run the database seeds for Aramex shipping configuration
     */
    public function run(): void
    {
        // 1. Create Aramex Carrier
        $aramex = ShippingCarrier::updateOrCreate(
            ['slug' => 'aramex'],
            [
                'name' => 'Aramex',
                'is_active' => true,
                'supports_tracking' => true,
                'supports_labels' => true,
                'supports_live_rates' => true,
                'cache_duration' => 60, // Cache live rates for 60 minutes
                'api_config' => [
                    'username' => 'testingapi@aramex.com',
                    'password' => 'R123456789$r',
                    'account_number' => '987654',
                    'account_pin' => '226321',
                    'account_entity' => 'CAI',
                    'account_country_code' => 'EG',
                    'is_production' => false // Sandbox mode
                ]
            ]
        );

        $this->command->info('✅ Aramex carrier created/updated');

        // 2. Create Shipping Zones

        // Egypt Domestic Zone
        $egyptZone = ShippingZone::updateOrCreate(
            ['name' => 'Egypt Domestic'],
            [
                'countries' => ['EG'],
                'is_active' => true
            ]
        );

        $this->command->info('✅ Egypt Domestic zone created');

        // International Zone (all other countries)
        $internationalZone = ShippingZone::updateOrCreate(
            ['name' => 'International'],
            [
                'countries' => ['*'], // Wildcard for all countries
                'is_active' => true
            ]
        );

        $this->command->info('✅ International zone created');

        // 3. Create Shipping Methods

        // Egypt Domestic Express (Live API)
        $domesticMethod = ShippingMethod::updateOrCreate(
            [
                'carrier_id' => $aramex->id,
                'zone_id' => $egyptZone->id,
                'service_code' => 'DOM'
            ],
            [
                'name' => 'Aramex Domestic Express',
                'rate_source' => 'live_api', // Use live Aramex API rates
                'estimated_days_min' => 1,
                'estimated_days_max' => 3,
                'is_active' => true,
                'sort_order' => 1
            ]
        );

        $this->command->info('✅ Aramex Domestic Express method created');

        // Egypt Domestic - Fixed Rate Fallback
        ShippingRate::updateOrCreate(
            ['method_id' => $domesticMethod->id],
            [
                'price_type' => 'fixed',
                'base_price' => 50.00, // Fallback price if API fails
                'weight_ranges' => null,
                'free_shipping_threshold' => 500.00 // Free shipping over 500 EGP
            ]
        );

        $this->command->info('✅ Domestic fallback rate created (50 EGP)');

        // International Express (Live API)
        $internationalMethod = ShippingMethod::updateOrCreate(
            [
                'carrier_id' => $aramex->id,
                'zone_id' => $internationalZone->id,
                'service_code' => 'EXP'
            ],
            [
                'name' => 'Aramex International Express',
                'rate_source' => 'live_api',
                'estimated_days_min' => 3,
                'estimated_days_max' => 7,
                'is_active' => true,
                'sort_order' => 2
            ]
        );

        $this->command->info('✅ Aramex International Express method created');

        // International - Fixed Rate Fallback
        ShippingRate::updateOrCreate(
            ['method_id' => $internationalMethod->id],
            [
                'price_type' => 'fixed',
                'base_price' => 250.00, // Fallback price for international
                'weight_ranges' => null,
                'free_shipping_threshold' => null // No free shipping internationally
            ]
        );

        $this->command->info('✅ International fallback rate created (250 EGP)');

        // Summary
        $this->command->newLine();
        $this->command->info('════════════════════════════════════════════════════');
        $this->command->info('🚀 Aramex Shipping Configuration Complete!');
        $this->command->info('════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('Carrier: Aramex (Sandbox Mode)');
        $this->command->info('Account: 987654');
        $this->command->info('Entity: CAI (Cairo)');
        $this->command->info('');
        $this->command->info('Zones:');
        $this->command->info('  • Egypt Domestic (EG)');
        $this->command->info('  • International (All countries)');
        $this->command->info('');
        $this->command->info('Methods:');
        $this->command->info('  • Aramex Domestic Express (Live API + 50 EGP fallback)');
        $this->command->info('  • Aramex International Express (Live API + 250 EGP fallback)');
        $this->command->info('');
        $this->command->info('Next Steps:');
        $this->command->info('  1. Test connection: php artisan tinker');
        $this->command->info('     then: $carrier = App\\Models\\ShippingCarrier::where(\'slug\', \'aramex\')->first();');
        $this->command->info('           $service = new App\\Services\\Shipping\\AramexService($carrier->api_config);');
        $this->command->info('           $service->testConnection();');
        $this->command->info('');
        $this->command->info('  2. Enable shipping: Set shipping_enabled=1 in business_settings');
        $this->command->info('  3. Test frontend checkout flow');
        $this->command->info('════════════════════════════════════════════════════');
    }
}
