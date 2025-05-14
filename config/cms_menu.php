<?php

return [
    [
        'title' => 'Home',
        'icon' => 'home.png',
        'route' => 'home',
        'is_route' => true,
    ],
    [
        'title' => 'Analytics',
        'icon' => 'analytics.png',
        'route' => 'home',
        'is_route' => true,
    ],
    [
        'title' => 'Pages',
        'icon' => 'pages.png',
        'children' => [
            ['title' => 'Add New Page', 'route' => 'brands.create', 'is_route' => true],
            ['title' => 'Pages List', 'route' => 'brands.index', 'is_route' => true],
        ]
    ],
    [
        'title' => 'Posts',
        'icon' => 'posts.png',
        'children' => [
            ['title' => 'Add Post', 'route' => 'cms.posts.create', 'is_route' => true],
            ['title' => 'Posts List', 'route' => 'brands.index', 'is_route' => true],
            ['title' => 'Categories', 'route' => 'brands.index', 'is_route' => true],
        ]
    ],
    [
        'title' => 'Media Gallery',
        'icon' => 'media_gallery.png',
        'route' => 'home',
        'is_route' => true,
    ],
    [
        'title' => 'Users',
        'icon' => 'users.png',
        'children' => [
            ['title' => 'Add new user', 'route' => 'brands.create', 'is_route' => true],
            ['title' => 'Users List', 'route' => 'brands.index', 'is_route' => true],
            ['title' => 'Profile', 'route' => 'brands.index', 'is_route' => true],
        ]
    ],
    [
        'title' => 'Settings',
        'icon' => 'setting.png',
        'route' => 'home',
        'is_route' => true,
    ],
    [
        'title' => 'ACF',
        'icon' => 'cms.png',
        'route' => 'home',
        'is_route' => true,
    ],
    [
        'title' => 'CPT',
        'icon' => 'cms.png',
        'route' => 'cms.cpt.index',
        'is_route' => true,
    ],
    [
        'title' => 'Menus',
        'icon' => 'menu.png',
        'route' => 'home',
        'is_route' => true,
    ],

];
