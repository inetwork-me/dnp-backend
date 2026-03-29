<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->json('metadata')->nullable();
            $table->foreignId('folder_id')->nullable()->constrained('media_folders')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('media');
    }
};
