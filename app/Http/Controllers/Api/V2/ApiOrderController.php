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
        $orders = Order::orderBy('created_at', 'desc')->paginate($perPage);
        // ->with('items.product')
        // ->get();

        // $coupons = Coupon::orderBy('created_at', 'desc')->paginate($perPage);
        return new OrderCollection($orders);
    }

    // GET /api/orders/{order}
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return $order->load('items.product', 'statusHistories.user');
    }


    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'status' => [
                'required',
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
        ]);

        // This triggers your OrderObserver->updating() and writes the history record
        $order->update(['status' => $data['status']]);

        // Reload to include the fresh history
        $order->load('statusHistories.user');

        return response()->json([
            'message' => 'Order status updated',
            'data'    => $order,
        ]);
    }
}
