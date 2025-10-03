<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class SuperAdminPermissionsSeeder extends Seeder
{
    /**
     * Seed super admin role and assign all permissions to user ID 1
     * Safe for production - only creates what doesn't exist
     *
     * @return void
     */
    public function run()
    {
        // Find user ID 1
        $superAdminUser = User::find(1);

        if (!$superAdminUser) {
            $this->command->error('User with ID 1 not found. Please create the user first.');
            return;
        }

        $this->command->info("Found user: {$superAdminUser->name} ({$superAdminUser->email})");

        // Create or get Super Admin role
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['guard_name' => 'web']
        );

        $this->command->info("Super Admin role: " . ($superAdminRole->wasRecentlyCreated ? 'created' : 'already exists'));

        // Get all existing permissions
        $allPermissions = Permission::all();

        if ($allPermissions->isEmpty()) {
            $this->command->warn('No permissions found in the database. Please run permissions seeder first.');
            return;
        }

        $this->command->info("Found {$allPermissions->count()} permissions in the system");

        // Assign all permissions to Super Admin role
        $superAdminRole->syncPermissions($allPermissions);
        $this->command->info("Assigned all {$allPermissions->count()} permissions to Super Admin role");

        // Assign Super Admin role to user ID 1 (if not already assigned)
        if (!$superAdminUser->hasRole('Super Admin')) {
            $superAdminUser->assignRole('Super Admin');
            $this->command->info("Assigned Super Admin role to user ID 1");
        } else {
            $this->command->info("User ID 1 already has Super Admin role");
        }

        // Summary
        $this->command->info('');
        $this->command->info('✅ Super Admin Setup Complete!');
        $this->command->info("User: {$superAdminUser->name}");
        $this->command->info("Email: {$superAdminUser->email}");
        $this->command->info("Roles: " . $superAdminUser->roles->pluck('name')->join(', '));
        $this->command->info("Total Permissions: " . $superAdminUser->getAllPermissions()->count());
    }
}
