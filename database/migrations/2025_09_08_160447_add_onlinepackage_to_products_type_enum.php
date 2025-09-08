<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE products MODIFY type ENUM('simple', 'session', 'bundle', 'package', 'onlinepackage', 'subscription', 'service') NOT NULL DEFAULT 'simple'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY type ENUM('simple', 'session', 'bundle', 'package', 'subscription', 'service') NOT NULL DEFAULT 'simple'");
    }
};
