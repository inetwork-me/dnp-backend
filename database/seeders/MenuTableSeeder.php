<?php 
// database/seeders/MenuTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class MenuTableSeeder extends Seeder
{
    public function run(): void
    {

        if (DB::table('menus')->count() > 0) {
            return;
        }
        Menu::create([
            'name' => 'Main Menu',
            'slug' => 'main',
            'is_default' => true,
            'items' => [
                [
                    'order' => 1,
                    'active' => true,
                    'title' => [
                        'en' => 'Home',
                        'ar' => 'الرئيسية',
                    ],
                    'url' => '/',
                    'page_id' => null,
                    'children' => [],
                ],
                [
                    'order' => 2,
                    'active' => true,
                    'title' => [
                        'en' => 'About',
                        'ar' => 'من نحن',
                    ],
                    'url' => '/about',
                    'page_id' => null,
                    'children' => [
                        [
                            'order' => 1,
                            'active' => true,
                            'title' => [
                                'en' => 'Team',
                                'ar' => 'فريق العمل',
                            ],
                            'url' => '/about/team',
                            'page_id' => null,
                            'children' => [],
                        ],
                        [
                            'order' => 2,
                            'active' => false,
                            'title' => [
                                'en' => 'History',
                                'ar' => 'تاريخنا',
                            ],
                            'url' => '/about/history',
                            'page_id' => null,
                            'children' => [],
                        ],
                    ],
                ],
                [
                    'order' => 3,
                    'active' => true,
                    'title' => [
                        'en' => 'Contact',
                        'ar' => 'اتصل بنا',
                    ],
                    'url' => '/contact',
                    'page_id' => null,
                    'children' => [],
                ],
            ],
        ]);
    }
}
