<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanLoyalty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:loyalty {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all loyalty points, transactions, and vouchers from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete ALL loyalty points, transactions, and vouchers data. Are you sure?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting loyalty cleanup...');

        try {
            // Get counts before deletion
            $transactionsCount = DB::table('loyalty_points_transactions')->count();
            $vouchersCount = DB::table('vouchers')->count();
            $customersCount = DB::table('customers')->count();

            // Use DB transaction wrapper
            DB::transaction(function () {
                // Delete in correct order to handle foreign key constraints
                $this->info('Deleting loyalty points transactions...');
                DB::table('loyalty_points_transactions')->delete();

                $this->info('Deleting vouchers...');
                DB::table('vouchers')->delete();

                $this->info('Resetting customer loyalty points...');
                DB::table('customers')->update([
                    'total_loyalty_points' => 0,
                    'used_loyalty_points' => 0,
                    'available_loyalty_points' => 0
                ]);

                // Reset auto-increment IDs
                $this->info('Resetting auto-increment counters...');
                DB::statement('ALTER TABLE loyalty_points_transactions AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE vouchers AUTO_INCREMENT = 1');
            });

            $this->info('✅ Loyalty cleanup completed successfully!');
            $this->table(
                ['Table', 'Records Deleted/Reset'],
                [
                    ['loyalty_points_transactions', $transactionsCount],
                    ['vouchers', $vouchersCount],
                    ['customers (points reset)', $customersCount],
                    ['Total', $transactionsCount + $vouchersCount]
                ]
            );

        } catch (\Exception $e) {
            $this->error('❌ Loyalty cleanup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}