<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Use firstOrCreate to only create if doesn't exist (won't overwrite existing data)
        $settings = [
            ['key' => 'site_logo', 'value' => ['default' => '']],
            ['key' => 'site_title', 'value' => ['en' => 'My Site', 'ar' => 'موقعي']],
            ['key' => 'site_tagline', 'value' => ['en' => 'Just another site', 'ar' => 'مجرد موقع آخر']],
            ['key' => 'contact_email', 'value' => ['default' => 'hello@example.com']],
            ['key' => 'contact_phone', 'value' => ['default' => '+20123456789']],
            ['key' => 'contact_address', 'value' => ['en' => '123 Main St', 'ar' => '١٢٣ شارع']],
            ['key' => 'social_facebook', 'value' => ['default' => 'https://facebook.com/']],
            ['key' => 'social_twitter', 'value' => ['default' => 'https://twitter.com/']],
            ['key' => 'social_instagram', 'value' => ['default' => 'https://instagram.com/']],
        ];

        foreach ($settings as $setting) {
            // firstOrCreate only creates if the key doesn't exist
            // It will NOT update/overwrite existing settings
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        $this->command->info('Settings seeded successfully (existing data preserved)');
    }
}
