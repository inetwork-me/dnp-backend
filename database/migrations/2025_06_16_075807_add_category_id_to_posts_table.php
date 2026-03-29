<?php

// database/migrations/xxxx_xx_xx_add_category_id_to_posts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            // FK to your post_type_categories table:
            $table->unsignedBigInteger('category_id')
                ->nullable()
                ->after('post_type_id');

            $table->foreign('category_id')
                ->references('id')
                ->on('post_type_categories')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
}
