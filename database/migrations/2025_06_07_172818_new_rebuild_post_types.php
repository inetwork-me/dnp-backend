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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('post_types');
        Schema::enableForeignKeyConstraints();

        Schema::create('post_types', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('label');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    
   {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('post_types');
        Schema::enableForeignKeyConstraints();
    } 
};
