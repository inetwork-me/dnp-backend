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
        Schema::create('carrier_ranges', function (Blueprint $table) {
            $table->id();
            $table->integer('carrier_id')->nullable();
            $table->string('billing_type')->nullable();
            $table->double('delimiter1',25,2)->nullable();
            $table->double('delimiter2',25,2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrier_ranges');
    }
};
