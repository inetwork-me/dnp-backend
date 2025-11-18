<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class V2PermissionsSeeder extends Seeder
{
    /**
     * Create all permissions based on V2 API routes
     * Modern naming: resource.action format
     * Safe for production - only creates what doesn't exist
     */
    public function run()
    {
        $this->command->info('🔐 Creating V2 API permissions...');

        // Define all permissions based on actual V2 routes
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Reviews (Admin)
            'reviews.view',
            'reviews.create',
            'reviews.edit',
            'reviews.delete',
            'reviews.manage',
            'reviews.toggle-status',

            // Shipping Carriers (Admin)
            'shipping.carriers.view',
            'shipping.carriers.create',
            'shipping.carriers.edit',
            'shipping.carriers.delete',
            'shipping.carriers.test',
            'shipping.carriers.toggle-status',

            // Shipping
            'shipping.view',
            'shipping.calculate-rates',
            'shipping.delivery-estimate',
            'shipping.quotes.view',
            'shipping.validate-address',

            // Shipments
            'shipments.view',
            'shipments.create',
            'shipments.track',
            'shipments.label.view',
            'shipments.status.update',

            // Brands
            'brands.view',
            'brands.create',
            'brands.edit',
            'brands.delete',
            'brands.manage',

            // Business Settings
            'business-settings.view',
            'business-settings.edit',

            // Coupons
            'coupons.view',
            'coupons.create',
            'coupons.edit',
            'coupons.delete',
            'coupons.manage',
            'coupons.apply',
            'coupons.redemptions.view',
            'coupons.usage.view',

            // Blocks (CMS)
            'blocks.view',
            'blocks.create',
            'blocks.edit',
            'blocks.delete',
            'blocks.manage',

            // BMI Settings
            'bmi-settings.view',
            'bmi-settings.create',
            'bmi-settings.edit',
            'bmi-settings.delete',
            'bmi-settings.manage',
            'bmi-settings.toggle-status',
            'bmi-settings.update-order',

            // Email Settings
            'email-settings.view',
            'email-settings.edit',
            'email-settings.test',

            // Folders (Media)
            'folders.view',
            'folders.create',
            'folders.edit',
            'folders.delete',
            'folders.reorder',

            // Forms (CMS)
            'forms.view',
            'forms.create',
            'forms.edit',
            'forms.delete',
            'forms.manage',
            'forms.fields.create',
            'forms.fields.edit',
            'forms.fields.delete',
            'forms.submissions.view',

            // Languages (CMS)
            'languages.view',
            'languages.create',
            'languages.edit',
            'languages.delete',
            'languages.manage',

            // Loyalty Program
            'loyalty.view',
            'loyalty.manage',
            'loyalty.customers.view',
            'loyalty.dashboard-stats.view',
            'loyalty.settings.view',
            'loyalty.settings.manage',
            'loyalty.summary.view',
            'loyalty.transactions.view',
            'loyalty.convert-to-voucher',
            'loyalty.manual-adjustment',

            // Media (CMS)
            'media.view',
            'media.create',
            'media.edit',
            'media.delete',
            'media.bulk-delete',

            // Menus (CMS)
            'menus.view',
            'menus.create',
            'menus.edit',
            'menus.delete',
            'menus.manage',
            'menus.set-default',

            // Orders
            'orders.view',
            'orders.edit',
            'orders.manage',
            'orders.status.update',

            // Permissions (CMS)
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'permissions.manage',
            'permissions.bulk-create',

            // Post Types (CMS)
            'post-types.view',
            'post-types.create',
            'post-types.edit',
            'post-types.delete',
            'post-types.manage',
            'post-types.categories.view',
            'post-types.categories.create',
            'post-types.categories.edit',
            'post-types.categories.delete',

            // Posts (CMS)
            'posts.view',
            'posts.create',
            'posts.edit',
            'posts.delete',
            'posts.manage',

            // Products
            'products.view',
            'products.create',
            'products.edit',
            'products.delete',
            'products.manage',
            'products.export',
            'products.reviews.view',

            // Product Categories
            'product-categories.view',
            'product-categories.create',
            'product-categories.edit',
            'product-categories.delete',
            'product-categories.manage',

            // Roles (CMS)
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.manage',
            'roles.permissions.assign',
            'roles.permissions.view',
            'roles.permissions.remove',

            // Settings (CMS)
            'settings.view',
            'settings.edit',
            'settings.manage',
            'settings.batch-update',

            // Tags (CMS)
            'tags.view',
            'tags.create',
            'tags.edit',
            'tags.delete',
            'tags.manage',

            // Transactions
            'transactions.view',
            'transactions.create',
            'transactions.download-invoice',

            // Users (CMS)
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'users.manage',
            'users.roles.assign',
            'users.roles.view',
            'users.roles.remove',
            'users.permissions.assign',
            'users.permissions.view',

            // Vouchers
            'vouchers.view',
            'vouchers.create',
            'vouchers.edit',
            'vouchers.delete',
            'vouchers.manage',
            'vouchers.redeem',
            'vouchers.redemptions.view',
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
                $this->command->info("  ✅ Created: {$permission}");
            } else {
                $existing++;
            }
        }

        $this->command->info('');
        $this->command->info("✅ Created {$created} new permissions");
        $this->command->info("ℹ️  {$existing} permissions already existed");

        // Assign all permissions to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        if ($superAdminRole) {
            $allPermissions = Permission::all();
            $superAdminRole->syncPermissions($allPermissions);
            $this->command->info("✅ Assigned all {$allPermissions->count()} permissions to Super Admin role");
        } else {
            $this->command->warn('⚠️  Super Admin role not found');
        }

        $this->command->info('');
        $this->command->info('🎉 V2 Permissions setup complete!');
        $this->command->info("📊 Total permissions in system: " . Permission::count());
    }
}
