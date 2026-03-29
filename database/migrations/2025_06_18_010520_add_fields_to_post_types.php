<?php

// database/migrations/2025_06_18_000001_add_fields_to_post_types.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPostTypes extends Migration
{
    public function up()
    {
        Schema::table('post_types', function (Blueprint $table) {
            $table->json('fields')
                ->nullable()
                ->after('slug');
        });
    }

    public function down()
    {
        Schema::table('post_types', fn (Blueprint $t) => $t->dropColumn('fields'));
    }
}
