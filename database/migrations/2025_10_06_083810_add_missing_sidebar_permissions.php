<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            // Brands
            ['name' => 'brands.manage', 'section' => 'brand'],

            // Coupons
            ['name' => 'coupons.manage', 'section' => 'coupon'],

            // Products
            ['name' => 'products.manage', 'section' => 'product'],

            // Categories
            ['name' => 'categories.manage', 'section' => 'product_category'],

            // Bundles
            ['name' => 'bundles.view', 'section' => 'bundle'],
            ['name' => 'bundles.manage', 'section' => 'bundle'],
            ['name' => 'bundles.create', 'section' => 'bundle'],
            ['name' => 'bundles.edit', 'section' => 'bundle'],
            ['name' => 'bundles.delete', 'section' => 'bundle'],

            // Packages
            ['name' => 'packages.view', 'section' => 'package'],
            ['name' => 'packages.manage', 'section' => 'package'],
            ['name' => 'packages.create', 'section' => 'package'],
            ['name' => 'packages.edit', 'section' => 'package'],
            ['name' => 'packages.delete', 'section' => 'package'],

            // Online Packages
            ['name' => 'onlinepackages.view', 'section' => 'onlinepackage'],
            ['name' => 'onlinepackages.manage', 'section' => 'onlinepackage'],
            ['name' => 'onlinepackages.create', 'section' => 'onlinepackage'],
            ['name' => 'onlinepackages.edit', 'section' => 'onlinepackage'],
            ['name' => 'onlinepackages.delete', 'section' => 'onlinepackage'],

            // Settings
            ['name' => 'settings.manage', 'section' => 'settings'],

            // Sessions
            ['name' => 'sessions.view', 'section' => 'session'],
            ['name' => 'sessions.manage', 'section' => 'session'],
            ['name' => 'sessions.create', 'section' => 'session'],
            ['name' => 'sessions.edit', 'section' => 'session'],
            ['name' => 'sessions.delete', 'section' => 'session'],

            // Branches
            ['name' => 'branches.view', 'section' => 'branch'],
            ['name' => 'branches.manage', 'section' => 'branch'],
            ['name' => 'branches.create', 'section' => 'branch'],
            ['name' => 'branches.edit', 'section' => 'branch'],
            ['name' => 'branches.delete', 'section' => 'branch'],

            // Orders
            ['name' => 'orders.manage', 'section' => 'order'],

            // Shipping
            ['name' => 'shipping.view', 'section' => 'shipping'],
            ['name' => 'shipping.manage', 'section' => 'shipping'],
            ['name' => 'shipping.carriers.manage', 'section' => 'shipping'],
            ['name' => 'shipping.shipments.view', 'section' => 'shipping'],
            ['name' => 'shipping.settings.manage', 'section' => 'shipping'],

            // Payment
            ['name' => 'payment.view', 'section' => 'payment'],
            ['name' => 'payment.manage', 'section' => 'payment'],
            ['name' => 'payment.settings.manage', 'section' => 'payment'],

            // Loyalty Program
            ['name' => 'loyalty.view', 'section' => 'loyalty'],
            ['name' => 'loyalty.manage', 'section' => 'loyalty'],
            ['name' => 'loyalty.customers.view', 'section' => 'loyalty'],
            ['name' => 'loyalty.vouchers.manage', 'section' => 'loyalty'],
            ['name' => 'loyalty.settings.manage', 'section' => 'loyalty'],

            // Media
            ['name' => 'media.view', 'section' => 'media'],
            ['name' => 'media.manage', 'section' => 'media'],
            ['name' => 'media.create', 'section' => 'media'],
            ['name' => 'media.edit', 'section' => 'media'],
            ['name' => 'media.delete', 'section' => 'media'],

            // Users
            ['name' => 'users.view', 'section' => 'user'],
            ['name' => 'users.manage', 'section' => 'user'],
            ['name' => 'users.create', 'section' => 'user'],
            ['name' => 'users.edit', 'section' => 'user'],
            ['name' => 'users.delete', 'section' => 'user'],

            // Roles
            ['name' => 'roles.manage', 'section' => 'role'],

            // Permissions
            ['name' => 'permissions.view', 'section' => 'permission'],
            ['name' => 'permissions.manage', 'section' => 'permission'],

            // Forms
            ['name' => 'forms.view', 'section' => 'form'],
            ['name' => 'forms.manage', 'section' => 'form'],
            ['name' => 'forms.create', 'section' => 'form'],
            ['name' => 'forms.edit', 'section' => 'form'],
            ['name' => 'forms.delete', 'section' => 'form'],

            // Menus
            ['name' => 'menus.view', 'section' => 'menu'],
            ['name' => 'menus.manage', 'section' => 'menu'],
            ['name' => 'menus.create', 'section' => 'menu'],
            ['name' => 'menus.edit', 'section' => 'menu'],
            ['name' => 'menus.delete', 'section' => 'menu'],

            // Blocks
            ['name' => 'blocks.view', 'section' => 'block'],
            ['name' => 'blocks.manage', 'section' => 'block'],
            ['name' => 'blocks.create', 'section' => 'block'],
            ['name' => 'blocks.edit', 'section' => 'block'],
            ['name' => 'blocks.delete', 'section' => 'block'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'guard_name' => 'web',
                    'section' => $permission['section'],
                    'is_legacy' => false,
                ]
            );
        }

        // Assign all new permissions to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            foreach ($permissions as $permission) {
                $perm = Permission::where('name', $permission['name'])->first();
                if ($perm && !$superAdminRole->hasPermissionTo($perm)) {
                    $superAdminRole->givePermissionTo($perm);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissionNames = [
            'brands.manage',
            'coupons.manage',
            'products.manage',
            'categories.manage',
            'bundles.view', 'bundles.manage', 'bundles.create', 'bundles.edit', 'bundles.delete',
            'packages.view', 'packages.manage', 'packages.create', 'packages.edit', 'packages.delete',
            'onlinepackages.view', 'onlinepackages.manage', 'onlinepackages.create', 'onlinepackages.edit', 'onlinepackages.delete',
            'settings.manage',
            'sessions.view', 'sessions.manage', 'sessions.create', 'sessions.edit', 'sessions.delete',
            'branches.view', 'branches.manage', 'branches.create', 'branches.edit', 'branches.delete',
            'orders.manage',
            'shipping.view', 'shipping.manage', 'shipping.carriers.manage', 'shipping.shipments.view', 'shipping.settings.manage',
            'payment.view', 'payment.manage', 'payment.settings.manage',
            'loyalty.view', 'loyalty.manage', 'loyalty.customers.view', 'loyalty.vouchers.manage', 'loyalty.settings.manage',
            'media.view', 'media.manage', 'media.create', 'media.edit', 'media.delete',
            'users.view', 'users.manage', 'users.create', 'users.edit', 'users.delete',
            'roles.manage',
            'permissions.view', 'permissions.manage',
            'forms.view', 'forms.manage', 'forms.create', 'forms.edit', 'forms.delete',
            'menus.view', 'menus.manage', 'menus.create', 'menus.edit', 'menus.delete',
            'blocks.view', 'blocks.manage', 'blocks.create', 'blocks.edit', 'blocks.delete',
        ];

        Permission::whereIn('name', $permissionNames)->delete();
    }
};
