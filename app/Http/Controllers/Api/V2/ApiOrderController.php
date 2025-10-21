<?php

// app/Http/Controllers/ApiOrderController.php
namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\OrderCollection;
use App\Models\Order;
use App\Services\StockTransactionService;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiOrderController extends Controller
{
    // GET /api/orders
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 20);
        $orders = Order::with(['user.customer', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return new OrderCollection($orders);
    }

    // GET /api/orders/{order}
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $orderData = $order->load([
            'items.product', 
            'statusHistories.user',
            'coupon',
            'shippingMethod.carrier',
            'shipments',
            'latestShipment',
            'loyaltyTransactions.product'
        ]);

        // Add calculated loyalty points earned
        $orderData->loyalty_points_earned = $order->totalLoyaltyPointsEarned();

        return $orderData;
    }


    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'status' => [
                'nullable',
                Rule::in([
                    'pending',
                    'processing',
                    'approved',
                    'shipped',
                    'refunded',
                    'completed',
                    'cancelled',
                ]),
            ],
            'payment_status' => [
                'nullable',
                Rule::in([
                    'unpaid',
                    'pending',
                    'paid',
                    'failed',
                    'refunded',
                ]),
            ],
        ]);

        // Update order status and/or payment status
        $updateData = array_filter($data, fn($value) => $value !== null);

        if (empty($updateData)) {
            return response()->json([
                'message' => 'No valid fields to update',
            ], 400);
        }

        // Check if order is being cancelled
        $oldStatus = $order->status;
        $newStatus = $updateData['status'] ?? null;
        $isCancelling = $newStatus === 'cancelled' && $oldStatus !== 'cancelled';

        // This triggers your OrderObserver->updating() and writes the history record
        $order->update($updateData);

        // Restore stock if order is cancelled
        if ($isCancelling) {
            $stockService = new StockTransactionService();
            $order->load('items.product');

            foreach ($order->items as $item) {
                if ($item->product) {
                    // Refresh product from database to get current stock
                    $item->product->refresh();

                    // Log the transaction BEFORE updating stock
                    $stockService->logOrderCancellation(
                        $item->product,
                        $item->quantity,
                        $order->id,
                        auth()->id()
                    );

                    // Then restore stock
                    $item->product->increment('current_stock', $item->quantity);
                }
            }
        }

        // Reload to include the fresh history
        $order->load('statusHistories.user');

        return response()->json([
            'message' => 'Order updated successfully',
            'data'    => $order,
        ]);
    }

    /**
     * Delete an order
     * Restores stock for all items before deletion (only if not already cancelled)
     */
    public function destroy(Order $order): JsonResponse
    {
        // Only restore stock if order wasn't already cancelled or refunded
        // (cancelled/refunded orders already had their stock restored)
        $shouldRestoreStock = !in_array($order->status, ['cancelled', 'refunded']);

        if ($shouldRestoreStock) {
            // Restore stock for all order items
            $stockService = new StockTransactionService();
            $order->load('items.product');

            foreach ($order->items as $item) {
                if ($item->product) {
                    // Refresh product from database to get current stock
                    $item->product->refresh();

                    // Log the transaction BEFORE updating stock
                    $stockService->logOrderCancellation(
                        $item->product,
                        $item->quantity,
                        $order->id,
                        auth()->id()
                    );

                    // Restore stock
                    $item->product->increment('current_stock', $item->quantity);
                }
            }
        }

        // Delete the order (cascade will delete items, status histories, etc.)
        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully',
        ]);
    }
}
