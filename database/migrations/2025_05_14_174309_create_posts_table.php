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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_type_id')->nullable()->constrained('post_types')->onDelete('set null');
            $table->string('slug')->unique(); // Slug for the default locale
            $table->string('featured_image')->nullable();
            $table->enum('status', ['draft', 'published', 'pending'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
