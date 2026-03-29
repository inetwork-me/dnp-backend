<?php

// database/migrations/2025_06_19_000003_create_form_submissions_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormSubmissionsTable extends Migration
{
    public function up()
    {
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->json('data');    // { fieldName: value, ... }
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('form_submissions');
    }
}
