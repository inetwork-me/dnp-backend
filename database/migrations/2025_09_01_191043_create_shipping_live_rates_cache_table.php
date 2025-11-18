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
        Schema::create('shipping_live_rates_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_id')->constrained('shipping_carriers')->onDelete('cascade');
            $table->json('origin_address'); // from address
            $table->json('destination_address'); // to address
            $table->json('package_details'); // weight, dimensions
            $table->json('rates_data'); // live rates response from API
            $table->json('delivery_estimates'); // estimated delivery dates
            $table->timestamp('expires_at'); // cache expiration
            $table->timestamps();
            
            // Index for faster lookups
            $table->index(['carrier_id', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_live_rates_cache');
    }
};
