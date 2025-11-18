<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductStockTransaction;
use Illuminate\Http\Request;

class StockTransactionController extends Controller
{
    /**
     * Get stock transactions for all products or a specific product
     *
     * GET /api/v2/stock-transactions
     * GET /api/v2/stock-transactions?product_id=123
     * GET /api/v2/stock-transactions?type=order
     * GET /api/v2/stock-transactions?start_date=2025-01-01&end_date=2025-12-31
     */
    public function index(Request $request)
    {
        $query = ProductStockTransaction::with(['product', 'user', 'order'])
            ->orderBy('created_at', 'desc');

        // Filter by product
        if ($request->has('product_id')) {
            $query->forProduct($request->product_id);
        }

        // Filter by transaction type
        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $transactions = $query->paginate($perPage);

        return response()->json($transactions);
    }

    /**
     * Get stock transactions for a specific product
     *
     * GET /api/v2/products/{product}/stock-transactions
     */
    public function forProduct(Request $request, Product $product)
    {
        $query = $product->stockTransactions()
            ->with(['user', 'order'])
            ->orderBy('created_at', 'desc');

        // Filter by type if provided
        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Pagination
        $perPage = $request->get('per_page', 20);
        $transactions = $query->paginate($perPage);

        return response()->json($transactions);
    }

    /**
     * Get stock transaction statistics for a product
     *
     * GET /api/v2/products/{product}/stock-transactions/stats
     */
    public function stats(Product $product)
    {
        $transactions = $product->stockTransactions;

        $stats = [
            'total_transactions' => $transactions->count(),
            'total_increased' => $transactions->where('quantity_change', '>', 0)->sum('quantity_change'),
            'total_decreased' => abs($transactions->where('quantity_change', '<', 0)->sum('quantity_change')),
            'by_type' => $transactions->groupBy('transaction_type')->map(function ($items, $type) {
                return [
                    'count' => $items->count(),
                    'total_change' => $items->sum('quantity_change'),
                ];
            }),
            'current_stock' => $product->current_stock,
        ];

        return response()->json($stats);
    }
}
