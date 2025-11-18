<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessSetting;

class EmailNotificationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'type' => 'admin_notification_emails',
                'value' => json_encode(['bolanaguib@gmail.com']),
                'lang' => null,
            ],
            [
                'type' => 'new_order_email_enabled',
                'value' => '1',
                'lang' => null,
            ],
            [
                'type' => 'order_status_change_email_enabled',
                'value' => '1',
                'lang' => null,
            ],
            [
                'type' => 'customer_welcome_email_enabled',
                'value' => '1',
                'lang' => null,
            ],
        ];

        foreach ($settings as $setting) {
            // Use firstOrCreate to avoid overwriting existing settings
            BusinessSetting::firstOrCreate(
                ['type' => $setting['type']],
                $setting
            );
        }
    }
}
