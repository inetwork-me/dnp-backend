<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();      // machine slug, e.g. "hero_section"
            $table->json('label');                 // {"en":"Hero","ar":"قسم البطل",…}
            $table->string('icon')->nullable();    // optional icon identifier
            $table->json('fields')->nullable(); // array of field‐schemas
            $table->json('settings')->nullable(); // array of field‐schemas
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blocks');
    }
};
