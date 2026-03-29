<?php

namespace App\Http\Controllers\Api\V2\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCarrier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ShippingCarrierController extends Controller
{
    public function index(): JsonResponse
    {
        $carriers = ShippingCarrier::with('shipments')
            ->withCount('shipments')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'carriers' => $carriers
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:shipping_carriers,slug',
            'api_config' => 'nullable|array',
            'is_active' => 'boolean',
            'supports_tracking' => 'boolean',
            'supports_labels' => 'boolean',
            'supports_live_rates' => 'boolean',
            'cache_duration' => 'integer|min:1|max:1440'
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $carrier = ShippingCarrier::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shipping carrier created successfully',
            'data' => [
                'carrier' => $carrier
            ]
        ], 201);
    }

    public function show(ShippingCarrier $carrier): JsonResponse
    {
        $carrier->load(['shipments' => function($query) {
            $query->latest()->take(10);
        }]);

        return response()->json([
            'success' => true,
            'data' => [
                'carrier' => $carrier
            ]
        ]);
    }

    public function update(Request $request, ShippingCarrier $carrier): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('shipping_carriers', 'slug')->ignore($carrier->id)
            ],
            'api_config' => 'sometimes|nullable|array',
            'is_active' => 'sometimes|boolean',
            'supports_tracking' => 'sometimes|boolean',
            'supports_labels' => 'sometimes|boolean',
            'supports_live_rates' => 'sometimes|boolean',
            'cache_duration' => 'sometimes|integer|min:1|max:1440'
        ]);

        $carrier->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Shipping carrier updated successfully',
            'data' => [
                'carrier' => $carrier->fresh()
            ]
        ]);
    }

    public function destroy(ShippingCarrier $carrier): JsonResponse
    {
        // Check if carrier has any shipments
        if ($carrier->shipments()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete carrier with existing shipments'
            ], 422);
        }

        $carrier->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shipping carrier deleted successfully'
        ]);
    }

    public function testConnection(ShippingCarrier $carrier): JsonResponse
    {
        try {
            $isConfigured = $carrier->isConfigured();
            
            if (!$isConfigured) {
                return response()->json([
                    'success' => false,
                    'data' => [
                        'is_configured' => false,
                        'message' => 'Carrier API configuration is incomplete'
                    ]
                ]);
            }

            // Test connection based on carrier type
            $result = $this->testCarrierConnection($carrier);

            return response()->json([
                'success' => $result['is_configured'],
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [
                    'is_configured' => false,
                    'message' => 'Connection test failed: ' . $e->getMessage()
                ]
            ]);
        }
    }

    private function testCarrierConnection(ShippingCarrier $carrier): array
    {
        switch (strtolower($carrier->slug)) {
            case 'aramex':
                $service = new \App\Services\Shipping\AramexService($carrier->api_config ?: []);
                return $service->testConnection();
                
            case 'dhl':
                // TODO: Implement DHL API test
                return [
                    'is_configured' => true,
                    'message' => 'DHL API test not implemented yet'
                ];
                
            case 'fedex':
                // TODO: Implement FedEx API test
                return [
                    'is_configured' => true,
                    'message' => 'FedEx API test not implemented yet'
                ];
                
            default:
                return [
                    'is_configured' => true,
                    'message' => 'Basic configuration validated'
                ];
        }
    }

    public function toggleStatus(ShippingCarrier $carrier): JsonResponse
    {
        $carrier->update([
            'is_active' => !$carrier->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Carrier status updated successfully',
            'data' => [
                'carrier' => $carrier->fresh()
            ]
        ]);
    }

    public function getSupportedCarriers(): JsonResponse
    {
        $supportedCarriers = [
            'aramex' => 'Aramex',
            'dhl' => 'DHL Express',
            'fedex' => 'FedEx',
            'ups' => 'UPS',
            'usps' => 'USPS',
            'local' => 'Local Delivery'
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'supported_carriers' => $supportedCarriers
            ]
        ]);
    }
}