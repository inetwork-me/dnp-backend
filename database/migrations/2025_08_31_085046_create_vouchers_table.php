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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            
            // Voucher Configuration
            $table->decimal('value', 8, 2); // Monetary value
            $table->string('currency', 3)->default('USD');
            $table->integer('points_used'); // Points deducted to create this voucher
            
            // Usage
            $table->enum('status', ['active', 'used', 'expired', 'cancelled'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->foreignId('used_in_order')->nullable()->constrained('orders')->onDelete('set null');
            
            // Admin tracking
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['customer_id', 'status']);
            $table->index(['code', 'status']);
            $table->index(['expires_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
