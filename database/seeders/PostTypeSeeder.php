<?php
// database/seeders/PostTypeSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PostTypeSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();
        DB::table('post_types')->insertOrIgnore([
            [
                'slug'       => 'page',
                'label'      => 'Page',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug'       => 'blog',
                'label'      => 'Blog',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
