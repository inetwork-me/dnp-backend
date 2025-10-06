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
        // Load cities from JSON file
        $jsonPath = base_path('../Aramix/cities.json');

        if (!file_exists($jsonPath)) {
            $this->command->error('cities.json not found at: ' . $jsonPath);
            return;
        }

        $cities = json_decode(file_get_contents($jsonPath), true);

        if (!$cities) {
            $this->command->error('Failed to parse cities.json');
            return;
        }

        $this->command->info('Seeding ' . count($cities) . ' cities...');

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

        $this->command->info('Successfully seeded ' . count($cities) . ' cities!');
    }
}
