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
        Schema::create('post_type_translations', function (Blueprint $table) {
            $table->id();
            $table->string('plural_label'); // e.g., 'Books'
            $table->string('singular_label'); // e.g., 'Book'
            $table->string('menu_name')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('post_type_id')->constrained('post_types')->onDelete('cascade');
            $table->string('lang')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_type_translations');
    }
};
