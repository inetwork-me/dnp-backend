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
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('method_id')->constrained('shipping_methods')->onDelete('cascade');
            $table->enum('price_type', ['fixed', 'weight_based', 'api_calculated'])->default('fixed');
            $table->decimal('base_price', 10, 2)->nullable(); // if fixed price
            $table->json('weight_ranges')->nullable(); // if weight-based pricing
            $table->decimal('free_shipping_threshold', 10, 2)->nullable(); // minimum order for free shipping
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_rates');
    }
};
