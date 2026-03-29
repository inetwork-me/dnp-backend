<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `form_fields` MODIFY COLUMN `type` ENUM(
            'text', 'email', 'phone', 'number', 'url', 'date', 'time', 'datetime',
            'textarea', 'media', 'select', 'radio', 'checkbox', 'relation', 'repeater',
            'link', 'boolean', 'file'
        ) NOT NULL DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `form_fields` MODIFY COLUMN `type` ENUM(
            'text', 'email', 'textarea', 'select', 'checkbox', 'radio', 'file'
        ) NOT NULL DEFAULT 'text'");
    }
};
