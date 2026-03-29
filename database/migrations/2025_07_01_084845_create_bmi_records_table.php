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
        Schema::create('bmi_records', function (Blueprint $table) {
            $table->id();
            $table->enum('gender', ['male', 'female']);
            $table->unsignedSmallInteger('age');
            $table->float('weight', 8, 2);            // in kilograms
            $table->float('height', 8, 2);            // in centimeters
            $table->enum('activity', [
                'sedentary',
                'light',
                'moderate',
                'active',
                'very_active'
            ]);
            $table->float('bmi', 5, 1);
            $table->string('bmi_category');
            $table->unsignedInteger('bmr');
            $table->unsignedInteger('tee');
            $table->unsignedInteger('calories');
            $table->float('water_intake_l', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bmi_records');
    }
};
