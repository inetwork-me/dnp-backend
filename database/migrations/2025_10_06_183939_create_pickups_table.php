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
        Schema::create('pickups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('pickup_guid')->unique()->nullable(); // Aramex pickup GUID
            $table->string('reference_number')->nullable(); // Internal reference
            $table->enum('status', ['pending', 'scheduled', 'in_progress', 'completed', 'cancelled'])->default('pending');

            // Pickup address
            $table->text('pickup_address_line1');
            $table->text('pickup_address_line2')->nullable();
            $table->string('pickup_city');
            $table->string('pickup_country', 2);
            $table->string('pickup_postal_code')->nullable();

            // Pickup contact
            $table->string('contact_person');
            $table->string('contact_company')->nullable();
            $table->string('contact_phone');
            $table->string('contact_email')->nullable();

            // Pickup schedule
            $table->dateTime('pickup_date');
            $table->time('ready_time');
            $table->time('last_pickup_time');
            $table->time('closing_time')->nullable();
            $table->string('pickup_location')->nullable(); // e.g., "Reception", "Warehouse"

            // Shipment details
            $table->integer('number_of_shipments')->default(1);
            $table->decimal('total_weight', 10, 2);
            $table->text('comments')->nullable();

            // API response data
            $table->json('carrier_response')->nullable();

            // Timestamps
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('pickup_guid');
            $table->index(['vendor_id', 'status']);
            $table->index('pickup_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pickups');
    }
};
