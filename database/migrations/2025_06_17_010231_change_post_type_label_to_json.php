<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Safely converts `label` string column to JSON `{ en, ar }` using a new column,
     * then drops the old and renames via raw SQL for MariaDB compatibility.
     */
    public function up(): void
    {
        if (Schema::hasColumn('post_types', 'label')) {
            Schema::table('post_types', function (Blueprint $table) {
                $table->dropColumn('label');
            });
        }

        // 1) Add a temporary JSON column if it doesn't exist
        if (!Schema::hasColumn('post_types', 'label')) {
            Schema::table('post_types', function (Blueprint $table) {
                $table->json('label')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * Revert JSON `label` back to simple string using an intermediate column,
     * and raw SQL for rename.
     */
    public function down(): void
    {
        // 1) Add back a string column if it doesn't exist
        if (!Schema::hasColumn('post_types', 'label_string')) {
            Schema::table('post_types', function (Blueprint $table) {
                $table->string('label_string')->nullable();
            });
        }

        // 2) Migrate JSON back to string (use `en` value)
        DB::table('post_types')
            ->select('id', 'label')
            ->orderBy('id')
            ->each(function ($row) {
                $data = json_decode($row->label, true) ?: [];
                $en = $data['en'] ?? '';

                DB::table('post_types')
                    ->where('id', $row->id)
                    ->update(['label_string' => $en]);
            });

        // 3) Drop JSON `label` column
        if (Schema::hasColumn('post_types', 'label')) {
            Schema::table('post_types', function (Blueprint $table) {
                $table->dropColumn('label');
            });
        }

        // 4) Rename `label_string` → `label` using raw SQL
        if (Schema::hasColumn('post_types', 'label_string')) {
            DB::statement('ALTER TABLE `post_types` CHANGE `label_string` `label` VARCHAR(255) NULL');
        }
    }
};
