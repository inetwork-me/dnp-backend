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
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_id')->constrained('shipping_carriers')->onDelete('cascade');
            $table->foreignId('zone_id')->constrained('shipping_zones')->onDelete('cascade');
            $table->string('name'); // "Express", "Standard"
            $table->string('service_code')->nullable(); // "EXP", "STD" - carrier's service code
            $table->integer('estimated_days_min')->nullable(); // minimum delivery days
            $table->integer('estimated_days_max')->nullable(); // maximum delivery days
            $table->enum('rate_source', ['fixed', 'live_api'])->default('fixed');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
