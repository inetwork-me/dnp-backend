<?php
// database/seeders/TagSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run()
    {
        // Creates an “Untagged” tag if it doesn’t already exist
        Tag::firstOrCreate(
            ['name' => 'Untagged']
        );
    }
}
