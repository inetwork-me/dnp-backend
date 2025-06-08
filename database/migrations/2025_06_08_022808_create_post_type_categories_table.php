<?php
// database/migrations/2025_06_08_000001_create_post_type_categories_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('post_type_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_type_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();       // e.g. 'events', 'tutorials'
            $table->json('name');                   // multilanguage: {"en":"Events","ar":"فعاليات"}
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('post_type_categories');
    }
};
