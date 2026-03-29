<?php

// database/migrations/2025_06_19_000002_create_form_fields_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormFieldsTable extends Migration
{
    public function up()
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->json('label');               // { en: "...", ar: "..." }
            $table->string('name');              // machine name
            $table->enum('type', [
                'text', 'email', 'textarea', 'select', 'checkbox', 'radio', 'file'
            ])->default('text');
            $table->json('options')->nullable();    // for select/radio: [{ value, label }]
            $table->json('validation')->nullable(); // e.g. { required: true, max:255 }
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('form_fields');
    }
}
