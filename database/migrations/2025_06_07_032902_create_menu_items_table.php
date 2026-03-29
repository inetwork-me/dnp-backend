<?php 

// database/migrations/xxxx_xx_xx_create_menus_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_default')->default(false);
            $table->json('items')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('menus');
    }
};
