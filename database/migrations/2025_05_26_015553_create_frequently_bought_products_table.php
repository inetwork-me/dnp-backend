<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrequentlyBoughtProductsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('frequently_bought_products', function (Blueprint $table) {
            // Primary key
            $table->id();

            // The new product (the one being duplicated into)
            $table->foreignId('product_id')
                ->constrained()              // assumes products.id
                ->onDelete('cascade');       // remove these entries if a product is deleted

            // The ID of the related frequently bought product
            $table->foreignId('frequently_bought_product_id')
                ->constrained('products')   // references products.id
                ->onDelete('cascade');

            // The category this “frequently bought together” relation belongs to
            $table->foreignId('category_id')
                ->constrained()              // assumes categories.id
                ->onDelete('cascade');

            // Track creation/update times
            $table->timestamps();

            // (Optional) prevent exact duplicates
            $table->unique([
                'product_id',
                'frequently_bought_product_id',
                'category_id'
            ], 'fbp_unique_triplet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frequently_bought_products');
    }
}
