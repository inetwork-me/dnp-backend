<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if cities already exist
        $existingCount = DB::table('aramex_cities')->count();
        if ($existingCount > 0) {
            $this->command->info("Aramex cities already seeded ({$existingCount} cities found). Skipping...");
            return;
        }

        // Try multiple possible paths for cities.json
        $possiblePaths = [
            base_path('../Aramix/cities.json'),
            base_path('storage/app/cities.json'),
            base_path('database/data/cities.json'),
        ];

        $jsonPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $jsonPath = $path;
                break;
            }
        }

        if (!$jsonPath) {
            $this->command->warn('cities.json not found. Tried paths:');
            foreach ($possiblePaths as $path) {
                $this->command->warn('  - ' . $path);
            }
            $this->command->info('Skipping cities seeder. You can add cities manually or place cities.json in one of the above paths.');
            return;
        }

        $cities = json_decode(file_get_contents($jsonPath), true);

        if (!$cities) {
            $this->command->error('Failed to parse cities.json');
            return;
        }

        $this->command->info('Seeding ' . count($cities) . ' cities from: ' . $jsonPath);

        // Insert in chunks for better performance
        $chunks = array_chunk($cities, 100);

        foreach ($chunks as $chunk) {
            $data = array_map(function($city) {
                return [
                    'aramex_city_id' => $city['aramex_city_id'],
                    'name' => $city['name'],
                    'country_code' => $city['country_code'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }, $chunk);

            DB::table('aramex_cities')->insert($data);
        }

        $this->command->info('✅ Successfully seeded ' . count($cities) . ' cities!');
    }
}
