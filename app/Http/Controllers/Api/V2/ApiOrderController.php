<?php

// app/Http/Controllers/ApiOrderController.php
namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\OrderCollection;
use App\Models\Order;
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

        // This triggers your OrderObserver->updating() and writes the history record
        $order->update($updateData);

        // Reload to include the fresh history
        $order->load('statusHistories.user');

        return response()->json([
            'message' => 'Order updated successfully',
            'data'    => $order,
        ]);
    }
}
