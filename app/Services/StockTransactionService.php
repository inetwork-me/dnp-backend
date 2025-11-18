<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductStockTransaction;
use Illuminate\Support\Facades\Auth;

class StockTransactionService
{
    /**
     * Log a stock transaction
     *
     * @param Product $product
     * @param int $quantityChange Negative for decrease, positive for increase
     * @param string $transactionType
     * @param string|null $reason
     * @param int|null $orderId
     * @param int|null $userId If null, uses current authenticated user
     * @return ProductStockTransaction
     */
    public function logTransaction(
        Product $product,
        int $quantityChange,
        string $transactionType,
        ?string $reason = null,
        ?int $orderId = null,
        ?int $userId = null
    ): ProductStockTransaction {
        $stockBefore = $product->current_stock;
        $stockAfter = $stockBefore + $quantityChange;

        return ProductStockTransaction::create([
            'product_id' => $product->id,
            'user_id' => $userId ?? Auth::id(),
            'order_id' => $orderId,
            'transaction_type' => $transactionType,
            'quantity_change' => $quantityChange,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'reason' => $reason,
        ]);
    }

    /**
     * Log stock decrease from order
     *
     * @param Product $product
     * @param int $quantity
     * @param int $orderId
     * @param int|null $userId
     * @return ProductStockTransaction
     */
    public function logOrderDecrease(
        Product $product,
        int $quantity,
        int $orderId,
        ?int $userId = null
    ): ProductStockTransaction {
        return $this->logTransaction(
            $product,
            -$quantity,
            ProductStockTransaction::TYPE_ORDER,
            "Stock decreased due to order #{$orderId}",
            $orderId,
            $userId
        );
    }

    /**
     * Log stock increase from order cancellation
     *
     * @param Product $product
     * @param int $quantity
     * @param int $orderId
     * @param int|null $userId
     * @return ProductStockTransaction
     */
    public function logOrderCancellation(
        Product $product,
        int $quantity,
        int $orderId,
        ?int $userId = null
    ): ProductStockTransaction {
        return $this->logTransaction(
            $product,
            $quantity,
            ProductStockTransaction::TYPE_CANCELLATION,
            "Stock restored from cancelled order #{$orderId}",
            $orderId,
            $userId
        );
    }

    /**
     * Log stock increase from cleanup command
     *
     * @param Product $product
     * @param int $quantity
     * @param int|null $orderId
     * @return ProductStockTransaction
     */
    public function logCleanupRestore(
        Product $product,
        int $quantity,
        ?int $orderId = null
    ): ProductStockTransaction {
        return $this->logTransaction(
            $product,
            $quantity,
            ProductStockTransaction::TYPE_CLEANUP,
            "Stock restored from cleanup command",
            $orderId,
            null // System operation, no user
        );
    }

    /**
     * Log manual stock adjustment
     *
     * @param Product $product
     * @param int $quantityChange
     * @param string $reason
     * @param int|null $userId
     * @return ProductStockTransaction
     */
    public function logManualAdjustment(
        Product $product,
        int $quantityChange,
        string $reason,
        ?int $userId = null
    ): ProductStockTransaction {
        return $this->logTransaction(
            $product,
            $quantityChange,
            ProductStockTransaction::TYPE_MANUAL_ADJUSTMENT,
            $reason,
            null,
            $userId
        );
    }

    /**
     * Log stock increase from return
     *
     * @param Product $product
     * @param int $quantity
     * @param int $orderId
     * @param string|null $reason
     * @param int|null $userId
     * @return ProductStockTransaction
     */
    public function logReturn(
        Product $product,
        int $quantity,
        int $orderId,
        ?string $reason = null,
        ?int $userId = null
    ): ProductStockTransaction {
        return $this->logTransaction(
            $product,
            $quantity,
            ProductStockTransaction::TYPE_RETURN,
            $reason ?? "Stock returned from order #{$orderId}",
            $orderId,
            $userId
        );
    }

    /**
     * Log stock restock
     *
     * @param Product $product
     * @param int $quantity
     * @param string|null $reason
     * @param int|null $userId
     * @return ProductStockTransaction
     */
    public function logRestock(
        Product $product,
        int $quantity,
        ?string $reason = null,
        ?int $userId = null
    ): ProductStockTransaction {
        return $this->logTransaction(
            $product,
            $quantity,
            ProductStockTransaction::TYPE_RESTOCK,
            $reason ?? "Stock replenished",
            null,
            $userId
        );
    }
}
