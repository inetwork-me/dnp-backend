<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModernPermissionsSeeder extends Seeder
{
    /**
     * Seed modern permission structure (resource.action format)
     * Safe for production - only creates permissions that don't exist
     */
    public function run()
    {
        $this->command->info('Creating modern permissions...');

        // Define modern permissions with resource.action format
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Brands
            'brands.view',
            'brands.manage',
            'brands.create',
            'brands.edit',
            'brands.delete',

            // Coupons
            'coupons.view',
            'coupons.manage',
            'coupons.create',
            'coupons.edit',
            'coupons.delete',

            // Products
            'products.view',
            'products.manage',
            'products.create',
            'products.edit',
            'products.delete',

            // Categories
            'categories.view',
            'categories.manage',
            'categories.create',
            'categories.edit',
            'categories.delete',

            // Bundles
            'bundles.view',
            'bundles.manage',
            'bundles.create',
            'bundles.edit',
            'bundles.delete',

            // Packages
            'packages.view',
            'packages.manage',
            'packages.create',
            'packages.edit',
            'packages.delete',

            // Online Packages
            'onlinepackages.view',
            'onlinepackages.manage',
            'onlinepackages.create',
            'onlinepackages.edit',
            'onlinepackages.delete',

            // Sessions
            'sessions.view',
            'sessions.manage',
            'sessions.create',
            'sessions.edit',
            'sessions.delete',

            // Orders
            'orders.view',
            'orders.manage',
            'orders.create',
            'orders.edit',
            'orders.delete',

            // Shipping
            'shipping.view',
            'shipping.manage',
            'shipping.carriers.manage',
            'shipping.shipments.view',
            'shipping.settings.manage',

            // Payment
            'payment.view',
            'payment.manage',
            'payment.settings.manage',

            // Loyalty
            'loyalty.view',
            'loyalty.manage',
            'loyalty.customers.view',
            'loyalty.vouchers.manage',
            'loyalty.settings.manage',

            // CMS - Media
            'media.view',
            'media.manage',
            'media.upload',
            'media.delete',

            // CMS - Users
            'users.view',
            'users.manage',
            'users.create',
            'users.edit',
            'users.delete',

            // CMS - Roles
            'roles.view',
            'roles.manage',
            'roles.create',
            'roles.edit',
            'roles.delete',

            // CMS - Permissions
            'permissions.view',
            'permissions.manage',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',

            // CMS - Forms
            'forms.view',
            'forms.manage',
            'forms.create',
            'forms.edit',
            'forms.delete',

            // CMS - Settings
            'settings.view',
            'settings.manage',

            // CMS - Menus
            'menus.view',
            'menus.manage',
            'menus.create',
            'menus.edit',
            'menus.delete',

            // CMS - Blocks
            'blocks.view',
            'blocks.manage',
            'blocks.create',
            'blocks.edit',
            'blocks.delete',

            // BMI Settings
            'bmi.settings.manage',
        ];

        $created = 0;
        $existing = 0;

        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );

            if ($perm->wasRecentlyCreated) {
                $created++;
            } else {
                $existing++;
            }
        }

        $this->command->info("✅ Created {$created} new permissions");
        $this->command->info("ℹ️  {$existing} permissions already existed");

        // Assign all new permissions to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        if ($superAdminRole) {
            $allPermissions = Permission::all();
            $superAdminRole->syncPermissions($allPermissions);
            $this->command->info("✅ Assigned all permissions to Super Admin role");
        } else {
            $this->command->warn('⚠️  Super Admin role not found. Run SuperAdminPermissionsSeeder first.');
        }

        $this->command->info('');
        $this->command->info('🎉 Modern permissions setup complete!');
        $this->command->info("Total permissions in system: " . Permission::count());
    }
}
