<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LoyaltySetting;

class LoyaltySettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'loyalty_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable/disable loyalty points system',
                'group' => 'general'
            ],
            [
                'key' => 'default_product_points',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Default points awarded for products without specific points set',
                'group' => 'general'
            ],
            [
                'key' => 'minimum_order_amount',
                'value' => '0',
                'type' => 'decimal',
                'description' => 'Minimum order amount to earn loyalty points',
                'group' => 'general'
            ],

            // Point Conversion Settings
            [
                'key' => 'points_to_currency_rate',
                'value' => '100',
                'type' => 'integer',
                'description' => 'How many points equal 1 unit of currency (100 points = $1)',
                'group' => 'conversion'
            ],
            [
                'key' => 'default_currency',
                'value' => 'EGP',
                'type' => 'string',
                'description' => 'Default currency for vouchers',
                'group' => 'conversion'
            ],

            // Expiry Settings
            [
                'key' => 'points_expire',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Do loyalty points expire?',
                'group' => 'expiry'
            ],
            [
                'key' => 'points_expiry_days',
                'value' => '365',
                'type' => 'integer',
                'description' => 'Days after which points expire (if enabled)',
                'group' => 'expiry'
            ],
            [
                'key' => 'voucher_expiry_days',
                'value' => '90',
                'type' => 'integer',
                'description' => 'Default expiry days for vouchers created from points',
                'group' => 'expiry'
            ],

            // Membership Tiers
            [
                'key' => 'bronze_tier_points',
                'value' => '0',
                'type' => 'integer',
                'description' => 'Points required for Bronze tier',
                'group' => 'tiers'
            ],
            [
                'key' => 'silver_tier_points',
                'value' => '1000',
                'type' => 'integer',
                'description' => 'Points required for Silver tier',
                'group' => 'tiers'
            ],
            [
                'key' => 'gold_tier_points',
                'value' => '5000',
                'type' => 'integer',
                'description' => 'Points required for Gold tier',
                'group' => 'tiers'
            ],
            [
                'key' => 'platinum_tier_points',
                'value' => '10000',
                'type' => 'integer',
                'description' => 'Points required for Platinum tier',
                'group' => 'tiers'
            ],

            // Bonus Settings
            [
                'key' => 'referral_points_referrer',
                'value' => '100',
                'type' => 'integer',
                'description' => 'Points awarded to referrer when someone signs up',
                'group' => 'bonus'
            ],
            [
                'key' => 'referral_points_referred',
                'value' => '50',
                'type' => 'integer',
                'description' => 'Points awarded to referred user when they sign up',
                'group' => 'bonus'
            ],
            [
                'key' => 'signup_bonus_points',
                'value' => '100',
                'type' => 'integer',
                'description' => 'Welcome bonus points for new customers',
                'group' => 'bonus'
            ],

            // Return/Refund Settings
            [
                'key' => 'deduct_points_on_return',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Deduct loyalty points when order is returned/cancelled',
                'group' => 'returns'
            ],
        ];

        foreach ($settings as $setting) {
            // Use firstOrCreate to avoid overwriting existing settings
            LoyaltySetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
