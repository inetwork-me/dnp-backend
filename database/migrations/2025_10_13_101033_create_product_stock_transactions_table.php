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
        Schema::create('product_stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('transaction_type', ['order', 'cancellation', 'manual_adjustment', 'return', 'cleanup', 'restock']);
            $table->integer('quantity_change'); // Negative for decrease, positive for increase
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->text('reason')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index('product_id');
            $table->index('transaction_type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stock_transactions');
    }
};
