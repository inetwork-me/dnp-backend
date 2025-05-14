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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_group_id')->constrained()->onDelete('cascade');
            $table->string('label');
            $table->string('name'); // system key ex: price, author_name
            $table->string('type'); // ex: text, textarea, image, select, checkbox
            $table->json('options')->nullable(); // لـ select, checkbox
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
