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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('added_by', 6)->default('admin');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('brand_id')->nullable();
            $table->string('photos', 2000)->nullable();
            $table->text('specification_images')->nullable();
            $table->string('thumbnail_img', 100)->nullable();
            $table->string('video_provider', 20)->nullable();
            $table->string('video_link', 100)->nullable();
            $table->string('tags', 500)->nullable();
            $table->longText('description')->nullable();
            $table->text('specifications')->nullable();
            $table->double('unit_price', 20, 2);
            $table->double('purchase_price', 20, 2)->nullable();
            $table->tinyInteger('variant_product')->default(0);
            $table->string('attributes', 1000)->default('[]');
            $table->mediumText('choice_options')->nullable();
            $table->mediumText('colors')->nullable();
            $table->text('variations')->nullable();
            $table->integer('todays_deal')->default(0);
            $table->integer('published')->default(1);
            $table->boolean('approved')->default(1);
            $table->string('stock_visibility_state', 10)->default('quantity');
            $table->boolean('cash_on_delivery')->default(0)->comment('1 = On, 0 = Off');
            $table->integer('featured')->default(0);
            $table->integer('seller_featured')->default(0);
            $table->integer('current_stock')->default(0);
            $table->string('unit', 20)->nullable();
            $table->double('weight', 8, 2)->default(0.00);
            $table->integer('min_qty')->default(1);
            $table->integer('low_stock_quantity')->nullable();
            $table->double('discount', 20, 2)->nullable();
            $table->string('discount_type', 10)->nullable();
            $table->integer('discount_start_date')->nullable();
            $table->integer('discount_end_date')->nullable();
            $table->double('starting_bid', 20, 2)->default(0.00);
            $table->integer('auction_start_date')->nullable();
            $table->integer('auction_end_date')->nullable();
            $table->double('tax', 20, 2)->nullable();
            $table->string('tax_type', 10)->nullable();
            $table->string('shipping_type', 20)->default('flat_rate');
            $table->double('shipping_cost', 20, 2)->default(0.00);
            $table->boolean('is_quantity_multiplied')->default(0)->comment('1 = Mutiplied with shipping cost');
            $table->integer('est_shipping_days')->nullable();
            $table->integer('num_of_sale')->default(0);
            $table->mediumText('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->string('meta_img', 255)->nullable();
            $table->string('pdf', 255)->nullable();
            $table->mediumText('slug');
            $table->double('rating', 8, 2)->default(0.00);
            $table->string('barcode', 255)->nullable();
            $table->tinyInteger('digital')->default(0);
            $table->tinyInteger('auction_product')->default(0);
            $table->string('file_name', 255)->nullable();
            $table->string('file_path', 255)->nullable();
            $table->string('external_link', 500)->nullable();
            $table->string('external_link_btn', 255)->default('Buy Now');
            $table->tinyInteger('wholesale_product')->default(0);
            $table->string('frequently_bought_selection_type', 19)->default('product');
            $table->date('product_service_day')->nullable();
            $table->time('product_service_start_time')->nullable();
            $table->time('product_service_end_time')->nullable();
            $table->text('product_service_location')->nullable();
            $table->string('sub_title', 255)->nullable();
            $table->text('product_service_adress')->nullable();
            $table->longText('product_service_custom_data')->charset('utf8mb4')->collation('utf8mb4_bin')->nullable()->check('json_valid(`product_service_custom_data`)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
