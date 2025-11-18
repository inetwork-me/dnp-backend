<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Validation\Rule;

class EmailSettingsController extends Controller
{
    /**
     * Get all email notification settings
     */
    public function index()
    {
        $settings = [
            'admin_notification_emails' => $this->getAdminEmails(),
            'new_order_email_enabled' => $this->getSettingValue('new_order_email_enabled', '1') === '1',
            'order_status_change_email_enabled' => $this->getSettingValue('order_status_change_email_enabled', '1') === '1',
            'customer_welcome_email_enabled' => $this->getSettingValue('customer_welcome_email_enabled', '1') === '1',
        ];

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update email notification settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'admin_notification_emails' => 'required|array|min:1',
            'admin_notification_emails.*' => 'required|email',
            'new_order_email_enabled' => 'required|boolean',
            'order_status_change_email_enabled' => 'required|boolean',
            'customer_welcome_email_enabled' => 'required|boolean',
        ]);

        try {
            // Update admin emails
            $this->updateSetting('admin_notification_emails', json_encode($request->admin_notification_emails));

            // Update email toggles
            $this->updateSetting('new_order_email_enabled', $request->new_order_email_enabled ? '1' : '0');
            $this->updateSetting('order_status_change_email_enabled', $request->order_status_change_email_enabled ? '1' : '0');
            $this->updateSetting('customer_welcome_email_enabled', $request->customer_welcome_email_enabled ? '1' : '0');

            return response()->json([
                'success' => true,
                'message' => 'Email settings updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update email settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test email notifications
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|in:new_order,status_change'
        ]);

        try {
            // Create test data for the notification
            $testData = [
                'order_number' => 'TEST-' . now()->format('Ymd-His'),
                'customer_name' => 'Test Customer',
                'total' => '150.00',
                'type' => $request->type
            ];

            \Illuminate\Support\Facades\Notification::route('mail', $request->email)
                ->notify(new \App\Notifications\TestEmailNotification($testData));

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $request->email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get admin notification emails
     */
    private function getAdminEmails()
    {
        $emails = $this->getSettingValue('admin_notification_emails', '[]');
        return json_decode($emails, true) ?: [];
    }

    /**
     * Get setting value by type
     */
    private function getSettingValue($type, $default = null)
    {
        $setting = BusinessSetting::where('type', $type)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Update or create setting
     */
    private function updateSetting($type, $value)
    {
        BusinessSetting::updateOrCreate(
            ['type' => $type],
            ['value' => $value, 'lang' => null]
        );
    }
}
