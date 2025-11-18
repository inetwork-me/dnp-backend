<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, get all existing branches and convert their data
        $branches = DB::table('branches')->get();

        foreach ($branches as $branch) {
            $addressJson = json_encode([
                'en' => $branch->address ?? '',
                'ar' => $branch->address ?? '',
            ]);

            $cityJson = json_encode([
                'en' => $branch->city ?? '',
                'ar' => $branch->city ?? '',
            ]);

            DB::table('branches')
                ->where('id', $branch->id)
                ->update([
                    'address' => $addressJson,
                    'city' => $cityJson,
                ]);
        }

        // Now change the column types to JSON
        Schema::table('branches', function (Blueprint $table) {
            $table->json('address')->nullable()->change();
            $table->json('city')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to text by extracting 'en' value
        $branches = DB::table('branches')->get();

        Schema::table('branches', function (Blueprint $table) {
            $table->text('address')->nullable()->change();
            $table->string('city')->nullable()->change();
        });

        foreach ($branches as $branch) {
            $address = json_decode($branch->address, true);
            $city = json_decode($branch->city, true);

            DB::table('branches')
                ->where('id', $branch->id)
                ->update([
                    'address' => $address['en'] ?? '',
                    'city' => $city['en'] ?? '',
                ]);
        }
    }
};
