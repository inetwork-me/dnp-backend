<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanReviews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:reviews {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all reviews and ratings from the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete ALL reviews and ratings data. Are you sure?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting reviews cleanup...');

        try {
            // Get counts before deletion
            $reviewsCount = DB::table('reviews')->count();

            // Use DB transaction wrapper
            DB::transaction(function () {
                $this->info('Deleting reviews...');
                DB::table('reviews')->delete();

                // Reset auto-increment ID
                $this->info('Resetting auto-increment counter...');
                DB::statement('ALTER TABLE reviews AUTO_INCREMENT = 1');
            });

            $this->info('✅ Reviews cleanup completed successfully!');
            $this->table(
                ['Table', 'Records Deleted'],
                [
                    ['reviews', $reviewsCount],
                    ['Total', $reviewsCount]
                ]
            );

        } catch (\Exception $e) {
            $this->error('❌ Reviews cleanup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}