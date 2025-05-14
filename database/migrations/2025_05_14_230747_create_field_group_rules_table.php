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
        Schema::create('field_group_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_group_id')->constrained()->onDelete('cascade');
            $table->string('key'); // e.g. 'post_type'
            $table->string('operator')->default('=='); // '==', '!=' ... etc
            $table->string('value'); // e.g. 'service', 'page'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_group_rules');
    }
};
