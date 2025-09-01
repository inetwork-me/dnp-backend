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
        Schema::create('shipping_quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->nullable()->constrained('carts')->onDelete('cascade');
            $table->json('destination_address');
            $table->json('available_methods'); // all shipping options with prices
            $table->foreignId('selected_method_id')->nullable()->constrained('shipping_methods')->onDelete('set null');
            $table->timestamp('expires_at'); // quote validity
            $table->timestamps();
            
            // Index for faster cart lookups
            $table->index(['cart_id', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_quotes');
    }
};
