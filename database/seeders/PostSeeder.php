<?php
// database/seeders/PostSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PostSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // fetch post_type IDs keyed by slug
        $types = DB::table('post_types')
            ->pluck('id', 'slug')
            ->toArray();

        DB::table('posts')->insertOrIgnore([
            [
                'post_type_id'  => $types['page'] ?? 1,
                'title'         => json_encode(['en' => 'Home', 'ar' => 'الرئيسية']),
                'slug'          => 'home',
                'content'       => json_encode([]),
                'featured_image'=> null,
                'status'        => 'published',
                'published_at'  => $now,
                'author_id'     => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'post_type_id'  => $types['blog'] ?? 2,
                'title'         => json_encode(['en' => 'Hello World', 'ar' => 'مرحباً بالعالم']),
                'slug'          => 'hello-world',
                'content'       => json_encode([]),
                'featured_image'=> null,
                'status'        => 'draft',
                'published_at'  => null,
                'author_id'     => null,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ]);
    }
}
