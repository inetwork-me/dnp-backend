<?php
// database/seeders/PostTypeCategorySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PostTypeCategorySeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // Fetch all existing post types
        $postTypes = DB::table('post_types')->select('id', 'slug')->get();

        foreach ($postTypes as $type) {
            DB::table('post_type_categories')->insertOrIgnore([
                'post_type_id' => $type->id,
                // unique slug per type
                'slug'         => "{$type->slug}-general",
                // multilingual name JSON
                'name'         => json_encode([
                    'en' => 'General',
                    'ar' => 'عام',
                ]),
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }
    }
}
