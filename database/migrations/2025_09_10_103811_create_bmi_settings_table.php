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
        Schema::create('bmi_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('bmi_range_from', 5, 2)->nullable(false);
            $table->decimal('bmi_range_to', 5, 2)->nullable(false);
            $table->json('classification')->nullable(false)->comment('Multilanguage classification like {"en": "Underweight", "ar": "نقص الوزن"}');
            $table->json('tips')->nullable(false)->comment('Multilanguage tips textarea');
            $table->json('recommended_water_intake')->nullable(false)->comment('Multilanguage water intake recommendation');
            $table->integer('order')->default(0)->comment('Display order');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Add indexes
            $table->index(['bmi_range_from', 'bmi_range_to']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bmi_settings');
    }
};
