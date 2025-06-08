<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminSeeder::class);
        $this->call([ProductSpecificationsTableSeeder::class]);
        $this->call([SettingTableSeeder::class]);
        $this->call([MenuTableSeeder::class]);
        $this->call([
    PostTypeSeeder::class,
    PostSeeder::class,

]);
                $this->call([PostTypeCategorySeeder::class]);


        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
