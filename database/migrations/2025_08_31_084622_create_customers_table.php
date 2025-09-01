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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Basic Info
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('phone')->nullable();
            
            // Address Information
            $table->text('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('billing_postal_code')->nullable();
            
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_country')->nullable();
            $table->string('shipping_postal_code')->nullable();
            
            // Customer Preferences
            $table->string('preferred_language', 10)->default('en');
            $table->string('preferred_currency', 10)->default('USD');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(false);
            
            // Loyalty Program
            $table->integer('total_loyalty_points')->default(0);
            $table->integer('used_loyalty_points')->default(0);
            $table->integer('available_loyalty_points')->default(0);
            
            // Customer Status
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->enum('membership_tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
            
            // Marketing
            $table->boolean('accepts_marketing')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('referral_code', 20)->unique()->nullable();
            $table->foreignId('referred_by')->nullable()->constrained('customers');
            
            // Analytics
            $table->decimal('lifetime_value', 10, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->decimal('average_order_value', 8, 2)->default(0);
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('membership_tier');
            $table->index('total_loyalty_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
