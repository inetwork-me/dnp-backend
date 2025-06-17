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
     */
    public function down(): void
    {
        Schema::table('post_types', function (Blueprint $table) {
            //
        });
    }
};
