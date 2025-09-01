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
        Schema::create('shipping_carriers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Aramex", "DHL", "FedEx"
            $table->string('slug')->unique(); // "aramex", "dhl", "fedex"
            $table->boolean('is_active')->default(true);
            $table->json('api_config')->nullable(); // API credentials, endpoints
            $table->boolean('supports_tracking')->default(false);
            $table->boolean('supports_labels')->default(false);
            $table->boolean('supports_live_rates')->default(false);
            $table->integer('cache_duration')->default(60); // minutes to cache live rates
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_carriers');
    }
};
