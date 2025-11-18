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
        Schema::table('orders', function (Blueprint $table) {
            // Add payment_intent_id column if it doesn't exist
            if (!Schema::hasColumn('orders', 'payment_intent_id')) {
                $table->string('payment_intent_id')->nullable()->after('payment_method');
            }
            // Add payment_gateway_response column if it doesn't exist
            if (!Schema::hasColumn('orders', 'payment_gateway_response')) {
                $table->json('payment_gateway_response')->nullable()->after('payment_intent_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_intent_id', 'payment_gateway_response']);
        });
    }
};
