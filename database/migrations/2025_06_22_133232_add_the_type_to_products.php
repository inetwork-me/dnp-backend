<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1️⃣ Add type enum to products
        Schema::table('products', function (Blueprint $table) {
            $table->enum('type', ['simple', 'bundle', 'subscription', 'service'])
                ->default('simple');
        });

        // 2️⃣ Create pivot table for bundle ↔ simple‐products
        Schema::create('bundle_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bundle_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->foreign('bundle_id')
                ->references('id')->on('products')
                ->onDelete('cascade');
            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade');
            $table->unique(['bundle_id', 'product_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bundle_product');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
