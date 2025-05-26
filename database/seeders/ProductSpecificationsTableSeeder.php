<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSpecificationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Example: seed a couple of specs for product #1 in default language
        DB::table('product_specifications')->insert([
            [
                'product_id' => 1,
                'key' => 'color',
                'value' => 'red',
                'language_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => 1,
                'key' => 'weight',
                'value' => '2kg',
                'language_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
