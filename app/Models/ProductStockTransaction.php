<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStockTransaction extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'transaction_type',
        'quantity_change',
        'stock_before',
        'stock_after',
        'reason',
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'stock_before' => 'integer',
        'stock_after' => 'integer',
    ];

    // Transaction type constants
    const TYPE_ORDER = 'order';
    const TYPE_CANCELLATION = 'cancellation';
    const TYPE_MANUAL_ADJUSTMENT = 'manual_adjustment';
    const TYPE_RETURN = 'return';
    const TYPE_CLEANUP = 'cleanup';
    const TYPE_RESTOCK = 'restock';

    /**
     * Get the product that owns the transaction
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who performed the transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with the transaction
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope to filter by product
     */
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Scope to filter by transaction type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
