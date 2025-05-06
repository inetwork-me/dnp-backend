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
        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->nullable();
            $table->string('name')->nullable();
            $table->string('unit')->nullable();
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();
            $table->string('product_service_adress')->nullable();
            $table->string('sub_title')->nullable();
            $table->text('product_service_custom_data')->nullable();
            $table->string('lang')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_translations');
    }
};
