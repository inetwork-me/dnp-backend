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
        Schema::table('form_fields', function (Blueprint $table) {
            $table->boolean('required')->default(false)->after('type');
            $table->boolean('supports_multilang')->default(false)->after('required');
            $table->boolean('multiple')->default(false)->after('supports_multilang');
            $table->json('conditional_logic')->nullable()->after('multiple');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_fields', function (Blueprint $table) {
            $table->dropColumn(['required', 'supports_multilang', 'multiple', 'conditional_logic']);
        });
    }
};
