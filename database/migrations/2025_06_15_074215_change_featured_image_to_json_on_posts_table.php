<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFeaturedImageToJsonOnPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            // make sure featured_image is nullable if needed
            $table->json('featured_image')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            // revert back to string (length 255)
            $table->string('featured_image', 255)->nullable()->change();
        });
    }
}
