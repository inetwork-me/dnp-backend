<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpgsPaymentService implements PaymentServiceInterface
{
    protected $merchantId;
    protected $username;
    protected $password;
    protected $apiUrl;
    protected $apiVersion;
    protected $frontendUrl;
    protected $isProduction;

    public function __construct()
    {
        $useTest = config('mpgs.use_test', true);
        $env = $useTest ? 'test' : 'production';

        $this->merchantId = config("mpgs.{$env}.merchant_id");
        $this->username = config("mpgs.{$env}.username");
        $this->password = config("mpgs.{$env}.password");
        $this->apiUrl = config("mpgs.{$env}.api_url");
        $this->apiVersion = config('mpgs.api_version', '100');
        $this->frontendUrl = config('mpgs.frontend_url');
        $this->isProduction = !$useTest;

        // Validate credentials
        if (!$this->merchantId || !$this->username || !$this->password) {
            throw new \Exception('MPGS credentials not configured. Check your .env file.');
        }
    }

    /**
     * Generate payment URL for Hosted Checkout
     */
    public function generatePaymentUrl(Order $order): array
    {
        try {
            // Step 1: Create MPGS session
            $sessionData = $this->createSession($order);

            if (!isset($sessionData['session']['id'])) {
                throw new \Exception('Failed to create MPGS session: No session ID returned');
            }

            $sessionId = $sessionData['session']['id'];

            // Step 2: Store session ID in order
            $order->update([
                'payment_intent_id' => $sessionId,
                'payment_gateway_response' => $sessionData,
            ]);

            // Step 3: Build Hosted Checkout URL
            $checkoutUrl = $this->buildHostedCheckoutUrl($sessionId);

            Log::info('MPGS Payment URL Generated', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'session_id' => $sessionId,
                'checkout_url' => $checkoutUrl,
                'environment' => $this->isProduction ? 'production' : 'test',
            ]);

            return [
                'success' => true,
                'payment_url' => $checkoutUrl,
                'session_id' => $sessionId,
            ];

        } catch (\Exception $e) {
            Log::error('MPGS Payment URL Generation Failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create MPGS session via API
     */
    protected function createSession(Order $order): array
    {
        $url = "{$this->apiUrl}/api/rest/version/{$this->apiVersion}/merchant/{$this->merchantId}/session";

        // Build return and cancel URLs
        $backendUrl = rtrim(config('app.url'), '/');
        $returnUrl = "{$backendUrl}/api/payment/mpgs/success?order_id={$order->id}";
        $cancelUrl = "{$backendUrl}/api/payment/mpgs/cancel?order_id={$order->id}";

        $payload = [
            'apiOperation' => 'INITIATE_CHECKOUT',
            'interaction' => [
                'operation' => 'PURCHASE',
                'returnUrl' => $returnUrl,
                'cancelUrl' => $cancelUrl,
                'merchant' => [
                    'name' => 'DR.NUTRITION',
                    'url' => $this->frontendUrl,
                ],
                'displayControl' => [
                    'billingAddress' => 'HIDE',
                    'shipping' => 'HIDE',
                ],
            ],
            'order' => [
                'id' => (string) $order->id,
                'amount' => number_format($order->total_amount, 2, '.', ''),
                'currency' => 'EGP',
                'description' => "Order #{$order->order_number}",
            ],
        ];

        Log::info('MPGS Session Creation Request', [
            'url' => $url,
            'merchant_id' => $this->merchantId,
            'order_id' => $order->id,
            'amount' => $payload['order']['amount'],
        ]);

        $response = Http::withBasicAuth($this->username, $this->password)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->timeout(30)
            ->post($url, $payload);

        if (!$response->successful()) {
            Log::error('MPGS Session Creation Failed', [
                'status' => $response->status(),
                'response' => $response->body(),
                'order_id' => $order->id,
            ]);
            throw new \Exception('MPGS API error: ' . $response->body());
        }

        $responseData = $response->json();

        Log::info('MPGS Session Created Successfully', [
            'order_id' => $order->id,
            'session_id' => $responseData['session']['id'] ?? 'unknown',
        ]);

        return $responseData;
    }

    /**
     * Build Hosted Checkout URL (redirect method)
     */
    protected function buildHostedCheckoutUrl(string $sessionId): string
    {
        return "{$this->apiUrl}/checkout/entry/{$sessionId}";
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(Order $order): array
    {
        try {
            $orderId = $order->id;
            $url = "{$this->apiUrl}/api/rest/version/{$this->apiVersion}/merchant/{$this->merchantId}/order/{$orderId}";

            Log::info('MPGS Payment Verification Request', [
                'order_id' => $orderId,
                'url' => $url,
            ]);

            $response = Http::withBasicAuth($this->username, $this->password)
                ->timeout(30)
                ->get($url);

            if (!$response->successful()) {
                throw new \Exception('Failed to verify payment with MPGS API');
            }

            $data = $response->json();

            Log::info('MPGS Payment Verified', [
                'order_id' => $order->id,
                'result' => $data['result'] ?? 'UNKNOWN',
                'status' => $data['status'] ?? 'UNKNOWN',
            ]);

            return [
                'success' => true,
                'status' => $data['status'] ?? 'UNKNOWN',
                'result' => $data['result'] ?? 'UNKNOWN',
                'data' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('MPGS Payment Verification Failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel payment (not needed for Hosted Checkout)
     */
    public function cancelPayment(Order $order): array
    {
        Log::info('MPGS Payment Cancellation Requested', [
            'order_id' => $order->id,
            'note' => 'Session will expire automatically',
        ]);

        return [
            'success' => true,
            'message' => 'Session will expire automatically',
        ];
    }
}
