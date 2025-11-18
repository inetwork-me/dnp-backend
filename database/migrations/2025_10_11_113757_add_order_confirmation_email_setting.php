<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\BusinessSetting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add order confirmation email enabled setting
        BusinessSetting::firstOrCreate(
            ['type' => 'order_confirmation_email_enabled'],
            [
                'value' => '1', // Enabled by default
                'lang' => null
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the setting
        BusinessSetting::where('type', 'order_confirmation_email_enabled')->delete();
    }
};
