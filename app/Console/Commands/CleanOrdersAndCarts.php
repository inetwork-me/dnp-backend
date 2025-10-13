<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\StockTransactionService;

class CleanOrdersAndCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:orders-carts {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all orders, order items, carts, and cart items from the database (with stock restoration)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will delete ALL orders and carts data. Are you sure?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('Starting cleanup...');

        try {
            // Get counts before deletion
            $orderItemsCount = DB::table('order_items')->count();
            $orderHistoriesCount = DB::table('order_status_histories')->count();
            $ordersCount = DB::table('orders')->count();
            $cartItemsCount = DB::table('cart_items')->count();
            $cartsCount = DB::table('carts')->count();

            // Use DB transaction wrapper instead of manual begin/commit
            DB::transaction(function () {
                // Restore stock BEFORE deleting order items
                $this->info('Restoring product stock...');
                $stockRestoredCount = $this->restoreProductStock();
                $this->info("✓ Restored stock for {$stockRestoredCount} order items");

                // Delete in correct order to handle foreign key constraints
                $this->info('Deleting order items...');
                DB::table('order_items')->delete();

                $this->info('Deleting order status histories...');
                DB::table('order_status_histories')->delete();

                $this->info('Deleting orders...');
                DB::table('orders')->delete();

                $this->info('Deleting cart items...');
                DB::table('cart_items')->delete();

                $this->info('Deleting carts...');
                DB::table('carts')->delete();

                // Reset auto-increment IDs
                $this->info('Resetting auto-increment counters...');
                DB::statement('ALTER TABLE order_items AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE order_status_histories AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE orders AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE cart_items AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE carts AUTO_INCREMENT = 1');
            });

            $this->info('✅ Cleanup completed successfully!');
            $this->table(
                ['Table', 'Records Deleted'],
                [
                    ['order_items', $orderItemsCount],
                    ['order_status_histories', $orderHistoriesCount],
                    ['orders', $ordersCount],
                    ['cart_items', $cartItemsCount],
                    ['carts', $cartsCount],
                    ['Total', $orderItemsCount + $orderHistoriesCount + $ordersCount + $cartItemsCount + $cartsCount]
                ]
            );

        } catch (\Exception $e) {
            $this->error('❌ Cleanup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Restore product stock from all order items
     *
     * @return int Number of order items processed
     */
    protected function restoreProductStock(): int
    {
        $orderItems = OrderItem::with('product')->get();
        $count = 0;
        $stockService = new StockTransactionService();

        foreach ($orderItems as $orderItem) {
            if ($orderItem->product) {
                // Increment current_stock back (reverse the decrement from order creation)
                $orderItem->product->increment('current_stock', $orderItem->quantity);

                // Log the stock restoration transaction
                $stockService->logCleanupRestore(
                    $orderItem->product,
                    $orderItem->quantity,
                    $orderItem->order_id
                );

                $count++;
            }
        }

        return $count;
    }
}
