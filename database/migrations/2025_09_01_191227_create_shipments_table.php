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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('carrier_id')->constrained('shipping_carriers')->onDelete('restrict');
            $table->string('tracking_number')->nullable();
            $table->enum('status', ['pending', 'booked', 'picked_up', 'in_transit', 'delivered', 'cancelled'])->default('pending');
            $table->json('carrier_response')->nullable(); // API response data
            $table->json('shipping_label')->nullable(); // label data/URL
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            
            // Indexes for tracking
            $table->index('tracking_number');
            $table->index(['order_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
