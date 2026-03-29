<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('posts', function (Blueprint $table) {
            // $table->foreignId('post_type_id')
            //       ->nullable()
            //       ->constrained('post_types')
            //       ->cascadeOnDelete();
            $table->foreignId('post_type_category_id')
                  ->nullable()
                  ->constrained('post_type_categories')
                  ->cascadeOnDelete();
        });
    }
    public function down() {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('post_type_category_id');
            $table->dropConstrainedForeignId('post_type_id');
        });
    }
};