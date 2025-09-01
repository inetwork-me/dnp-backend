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
        Schema::create('loyalty_points_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            
            $table->enum('type', [
                'earned', 'redeemed', 'expired', 'refunded', 
                'manual_add', 'manual_deduct', 'bonus'
            ]);
            $table->integer('points');
            $table->text('description')->nullable();
            
            // Reference data
            $table->json('metadata')->nullable(); // Store order details, product info, etc.
            
            // Admin tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('source')->default('system'); // system, admin, api, etc.
            
            $table->timestamps();
            
            $table->index(['customer_id', 'type']);
            $table->index(['customer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_points_transactions');
    }
};
