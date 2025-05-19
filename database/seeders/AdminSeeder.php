<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminExists = \DB::table('users')->where('email', 'admin@admin.com')->exists();
        
        if (!$adminExists) {
            \DB::table('users')->insert([
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
                'user_type' => 'admin',
                'phone' => '123456789',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
        }
    }
}
