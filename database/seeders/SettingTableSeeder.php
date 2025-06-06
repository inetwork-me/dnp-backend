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
        // In a DatabaseSeeder or tinker:
        Setting::updateOrCreate(
            ['key' => 'site_logo'],
            ['value' => ['default' => '']]
        );
        Setting::updateOrCreate(
            ['key' => 'site_title'],
            ['value' => ['en' => 'My Site', 'ar' => 'موقعي']]
        );
        Setting::updateOrCreate(
            ['key' => 'site_tagline'],
            ['value' => ['en' => 'Just another site', 'ar' => 'مجرد موقع آخر']]
        );
        Setting::updateOrCreate(
            ['key' => 'contact_email'],
            ['value' => ['default' => 'hello@example.com']]
        );
        Setting::updateOrCreate(
            ['key' => 'contact_phone'],
            ['value' => ['default' => '+20123456789']]
        );
        Setting::updateOrCreate(
            ['key' => 'contact_address'],
            ['value' => ['en' => '123 Main St', 'ar' => '١٢٣ شارع']]
        );
        Setting::updateOrCreate(
            ['key' => 'social_facebook'],
            ['value' => ['default' => 'https://facebook.com/']]
        );
        Setting::updateOrCreate(
            ['key' => 'social_twitter'],
            ['value' => ['default' => 'https://twitter.com/']]
        );
        Setting::updateOrCreate(
            ['key' => 'social_instagram'],
            ['value' => ['default' => 'https://instagram.com/']]
        );
    }
}
