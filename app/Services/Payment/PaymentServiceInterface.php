<?php

namespace App\Services\Payment;

use App\Models\Order;

interface PaymentServiceInterface
{
    /**
     * Generate payment URL for an order
     *
     * @param Order $order
     * @return array ['success' => bool, 'payment_url' => string, 'session_id' => string]
     */
    public function generatePaymentUrl(Order $order): array;

    /**
     * Verify payment status
     *
     * @param Order $order
     * @return array ['success' => bool, 'status' => string, 'data' => array]
     */
    public function verifyPayment(Order $order): array;

    /**
     * Cancel/void payment
     *
     * @param Order $order
     * @return array ['success' => bool, 'message' => string]
     */
    public function cancelPayment(Order $order): array;
}
