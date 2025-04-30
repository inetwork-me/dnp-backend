<?php

return [
    [
        'title' => 'Home',
        'icon' => 'home.png',
        'route' => 'home',
        'is_route' => true,
    ],
    [
        'title' => 'Vendors',
        'icon' => 'store.png',
        'children' => [
            ['title' => 'Create Vendor', 'route' => '#', 'is_route' => false],
            ['title' => 'Vendors List', 'route' => '#', 'is_route' => false],
            ['title' => 'Vendors Grid', 'route' => '#', 'is_route' => false],
        ]
    ],
    [
        'title' => 'Coupons',
        'icon' => 'sale.png',
        'route' => 'page1.html',
        'is_route' => false,
    ],
    [
        'title' => 'Brands',
        'icon' => 'store.png',
        'children' => [
            ['title' => 'New Brand', 'route' => 'brands.create', 'is_route' => true],
            ['title' => 'All Brands', 'route' => 'brands.index', 'is_route' => true],
            ['title' => 'Upload Brands', 'route' => 'brand_bulk_upload.index', 'is_route' => true],
        ]
    ],
    [
        'title' => 'Products',
        'icon' => 'cube.png',
        'children' => [
            ['title' => 'Create New Product', 'route' => 'products.create', 'is_route' => true],
            ['title' => 'All Products', 'route' => 'products.all', 'is_route' => true],
            [
                'title' => 'Categories',
                'children' => [
                    ['title' => 'Add Category', 'route' => 'categories.create', 'is_route' => true],
                    ['title' => 'Categories List', 'route' => 'categories.index', 'is_route' => true],
                    ['title' => 'Category Discounts', 'route' => 'categories_wise_product_discount', 'is_route' => true],
                ]
            ],
            ['title' => 'Attributes', 'route' => 'attributes.index', 'is_route' => true],
            ['title' => 'VAT & Tax', 'route' => 'vats.make.products', 'is_route' => true],
        ]
    ],
    [
        'title' => 'Orders',
        'icon' => 'bag.png',
        'route' => 'page1.html',
        'is_route' => false,
    ],
    [
        'title' => 'Admin & Permissions',
        'icon' => 'add_user.png',
        'children' => [
            ['title' => 'Admins List', 'route' => 'staffs.index', 'is_route' => true],
            ['title' => 'Roles', 'route' => 'roles.index', 'is_route' => true],
            ['title' => 'Add Admin', 'route' => '#', 'is_route' => false],
        ]
    ],
    [
        'title' => 'News & Blogs',
        'icon' => 'add_user.png',
        'children' => [
            ['title' => 'Category', 'route' => 'blog-category.index', 'is_route' => true],
            ['title' => 'All Blogs', 'route' => 'blog.index', 'is_route' => true],
        ]
    ],
    [
        'title' => 'Customer',
        'icon' => 'users.png',
        'route' => 'page1.html',
        'is_route' => false,
    ],
    [
        'title' => 'Invoices',
        'icon' => 'receipt.png',
        'route' => 'page1.html',
        'is_route' => false,
    ],
    [
        'title' => 'Inventory',
        'icon' => 'shop.png',
        'route' => 'page1.html',
        'is_route' => false,
    ],
    [
        'title' => 'Packages',
        'icon' => 'badge.png',
        'route' => 'page1.html',
        'is_route' => false,
    ],
];
