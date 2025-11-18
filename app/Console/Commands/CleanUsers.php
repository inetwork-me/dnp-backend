<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:users {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all users except admin@admin.com';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete ALL users except admin@admin.com. Are you sure?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting users cleanup...');

        try {
            // Check if admin@admin.com exists
            $adminUser = DB::table('users')->where('email', 'admin@admin.com')->first();
            if (!$adminUser) {
                $this->error('❌ Admin user (admin@admin.com) not found! Cannot proceed.');
                return 1;
            }

            // Get counts before deletion
            $totalUsersCount = DB::table('users')->count();
            $usersToDeleteCount = DB::table('users')
                ->where('email', '!=', 'admin@admin.com')
                ->count();

            if ($usersToDeleteCount === 0) {
                $this->info('✅ No users to delete. Only admin@admin.com exists.');
                return 0;
            }

            // Use DB transaction wrapper
            DB::transaction(function () use ($adminUser) {
                // Delete related data first (cascade delete should handle this, but being explicit)
                $this->info('Deleting user-related data...');

                // Delete customers (except admin's if exists)
                $customersDeleted = DB::table('customers')
                    ->where('user_id', '!=', $adminUser->id)
                    ->delete();

                // Delete users (except admin)
                $this->info('Deleting users...');
                $usersDeleted = DB::table('users')
                    ->where('email', '!=', 'admin@admin.com')
                    ->delete();

                $this->info("Deleted {$usersDeleted} users and {$customersDeleted} customers.");
            });

            $this->info('✅ Users cleanup completed successfully!');
            $this->table(
                ['Action', 'Count'],
                [
                    ['Total users before cleanup', $totalUsersCount],
                    ['Users deleted', $usersToDeleteCount],
                    ['Users remaining', 1],
                    ['Admin preserved', 'admin@admin.com']
                ]
            );

        } catch (\Exception $e) {
            $this->error('❌ Users cleanup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}