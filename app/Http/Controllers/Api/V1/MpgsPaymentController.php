<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\MpgsPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpgsPaymentController extends Controller
{
    protected $mpgsService;

    public function __construct(MpgsPaymentService $mpgsService)
    {
        $this->mpgsService = $mpgsService;
    }

    /**
     * Handle successful payment redirect from MPGS
     * GET /api/payment/mpgs/success?order_id=123&resultIndicator=XXX
     */
    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        $resultIndicator = $request->query('resultIndicator');

        Log::info('MPGS Payment Success Callback', [
            'order_id' => $orderId,
            'resultIndicator' => $resultIndicator,
            'all_params' => $request->all(),
        ]);

        if (!$orderId) {
            Log::error('MPGS Success: Missing order_id');
            return redirect(config('mpgs.frontend_url') . '/checkout?error=missing_order_id');
        }

        $order = Order::find($orderId);

        if (!$order) {
            Log::error('MPGS Success: Order not found', ['order_id' => $orderId]);
            return redirect(config('mpgs.frontend_url') . '/checkout?error=order_not_found');
        }

        // Verify payment with MPGS
        $verification = $this->mpgsService->verifyPayment($order);

        if ($verification['success'] && $verification['result'] === 'SUCCESS') {
            // Update order to paid
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing',
                'payment_gateway_response' => array_merge(
                    $order->payment_gateway_response ?? [],
                    ['callback' => $verification['data']]
                ),
            ]);

            Log::info('MPGS Payment Confirmed', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $order->total_amount,
            ]);

            // Redirect to frontend success page
            return redirect(config('mpgs.frontend_url') . '/checkout/status?success=true&order_id=' . $orderId);
        } else {
            // Payment failed
            $order->update([
                'payment_status' => 'failed',
                'payment_gateway_response' => array_merge(
                    $order->payment_gateway_response ?? [],
                    ['callback_failed' => $verification]
                ),
            ]);

            Log::warning('MPGS Payment Failed Verification', [
                'order_id' => $order->id,
                'result' => $verification['result'] ?? 'UNKNOWN',
                'error' => $verification['error'] ?? 'Verification failed',
            ]);

            return redirect(config('mpgs.frontend_url') . '/checkout/status?success=false&order_id=' . $orderId);
        }
    }

    /**
     * Handle cancelled payment redirect from MPGS
     * GET /api/payment/mpgs/cancel?order_id=123
     */
    public function cancel(Request $request)
    {
        $orderId = $request->query('order_id');

        Log::info('MPGS Payment Cancelled', [
            'order_id' => $orderId,
            'all_params' => $request->all(),
        ]);

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                $order->update([
                    'payment_status' => 'failed',
                    'payment_gateway_response' => array_merge(
                        $order->payment_gateway_response ?? [],
                        ['cancelled' => true, 'cancelled_at' => now()]
                    ),
                ]);

                Log::info('MPGS Order Marked as Cancelled', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]);
            }
        }

        // Redirect to frontend checkout page with cancelled flag
        return redirect(config('mpgs.frontend_url') . '/checkout?cancelled=true&order_id=' . $orderId);
    }
}
