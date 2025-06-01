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
        Schema::table('brand_translations', function (Blueprint $table) {
            // Add the two columns if they don't already exist
            if (!Schema::hasColumn('brand_translations', 'meta_title')) {
                $table->string('meta_title', 100)->nullable()->after('name');
            }
            if (!Schema::hasColumn('brand_translations', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brand_translations', function (Blueprint $table) {
            // Drop them when rolling back
            if (Schema::hasColumn('brand_translations', 'meta_description')) {
                $table->dropColumn('meta_description');
            }
            if (Schema::hasColumn('brand_translations', 'meta_title')) {
                $table->dropColumn('meta_title');
            }
        });
    }
};
