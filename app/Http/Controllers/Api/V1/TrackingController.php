<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Services\Shipping\AramexService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class TrackingController extends Controller
{
    public function track(Request $request): JsonResponse
    {
        $validated = $request->validate(['tracking_number' => 'required|string']);

        try {
            $shipment = Shipment::with(['order.user', 'carrier'])
                ->where('tracking_number', $validated['tracking_number'])
                ->first();

            if (!$shipment) {
                return response()->json(['success' => false, 'message' => 'Shipment not found with this tracking number'], 404);
            }

            $trackingInfo = [];
            if ($shipment->carrier->slug === 'aramex') {
                try {
                    $aramexConfig = [
                        'username' => config('shipping.aramex.username'),
                        'password' => config('shipping.aramex.password'),
                        'account_number' => config('shipping.aramex.account_number'),
                        'account_pin' => config('shipping.aramex.account_pin'),
                        'account_entity' => config('shipping.aramex.account_entity', 'CAI'),
                        'account_country_code' => config('shipping.aramex.account_country_code', 'EG'),
                        'is_production' => config('shipping.aramex.is_production', false)
                    ];

                    $aramexService = new AramexService($aramexConfig);
                    $result = $aramexService->trackShipments($validated['tracking_number'], false);
                    $trackingInfo = $result['tracking_results'];
                } catch (Exception $e) {
                    Log::warning('Real-time tracking failed, using database info', [
                        'tracking_number' => $validated['tracking_number'],
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'tracking_number' => $shipment->tracking_number,
                    'status' => $shipment->status,
                    'carrier' => [
                        'name' => $shipment->carrier->name,
                        'slug' => $shipment->carrier->slug
                    ],
                    'order_id' => $shipment->order_id,
                    'shipped_at' => $shipment->shipped_at,
                    'delivered_at' => $shipment->delivered_at,
                    'tracking_events' => $trackingInfo,
                    'estimated_delivery' => $this->calculateEstimatedDelivery($shipment)
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Tracking failed', ['tracking_number' => $validated['tracking_number'], 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to retrieve tracking information'], 500);
        }
    }

    public function validateAddress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|size:2'
        ]);

        try {
            $aramexConfig = [
                'username' => config('shipping.aramex.username'),
                'password' => config('shipping.aramex.password'),
                'account_number' => config('shipping.aramex.account_number'),
                'account_pin' => config('shipping.aramex.account_pin'),
                'account_entity' => config('shipping.aramex.account_entity', 'CAI'),
                'account_country_code' => config('shipping.aramex.account_country_code', 'EG'),
                'is_production' => config('shipping.aramex.is_production', false)
            ];

            $aramexService = new AramexService($aramexConfig);
            $result = $aramexService->validateAddress($validated);

            return response()->json([
                'success' => true,
                'data' => [
                    'is_valid' => $result['is_valid'],
                    'suggestions' => $result['suggestions'] ?? [],
                    'message' => $result['is_valid'] ? 'Address is valid' : 'Address validation failed. Check suggestions.'
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Address validation failed', ['address' => $validated, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to validate address: ' . $e->getMessage()], 500);
        }
    }

    private function calculateEstimatedDelivery($shipment): ?string
    {
        if ($shipment->status === 'delivered') return null;
        $estimatedDays = 3;
        return $shipment->shipped_at ? $shipment->shipped_at->addDays($estimatedDays)->format('Y-m-d') : now()->addDays($estimatedDays)->format('Y-m-d');
    }
}
