<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the setting already exists
        $exists = \DB::table('business_settings')
            ->where('type', 'online_payment_enabled')
            ->exists();

        if (!$exists) {
            \DB::table('business_settings')->insert([
                'type' => 'online_payment_enabled',
                'value' => '1', // Default to enabled
                'lang' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::table('business_settings')
            ->where('type', 'online_payment_enabled')
            ->delete();
    }
};
