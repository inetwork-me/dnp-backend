<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\ShippingCarrier;
use App\Models\ShippingZone;
use App\Models\ShippingMethod;
use App\Models\ShippingQuote;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ShippingController extends Controller
{
    public function calculateRates(Request $request): JsonResponse
    {
        // Check if shipping is enabled
        if (!is_shipping_enabled()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'rates' => [],
                    'shipping_disabled' => true,
                    'message' => 'Shipping is currently disabled'
                ]
            ]);
        }

        $validated = $request->validate([
            'origin' => 'required|array',
            'origin.line1' => 'required|string',
            'origin.city' => 'required|string',
            'origin.country' => 'required|string',
            'origin.postal_code' => 'nullable|string',
            'destination' => 'required|array',
            'destination.line1' => 'required|string',
            'destination.city' => 'required|string',
            'destination.country' => 'required|string',
            'destination.postal_code' => 'nullable|string',
            'packages' => 'nullable|array|min:1',
            'packages.*.weight' => 'nullable|numeric|min:0.1',
            'packages.*.length' => 'nullable|numeric|min:0.1',
            'packages.*.width' => 'nullable|numeric|min:0.1',
            'packages.*.height' => 'nullable|numeric|min:0.1',
            'cart_id' => 'nullable|exists:carts,id'
        ]);

        // Calculate total weight from cart if cart_id provided, otherwise use packages
        $hasOnlyFreeShippingItems = false;

        if (!empty($validated['cart_id'])) {
            $cart = Cart::with('items.product')->find($validated['cart_id']);
            $totalWeight = 0;
            $validated['packages'] = [];
            $allItemsFreeShipping = true;

            if ($cart) {
                foreach ($cart->items as $item) {
                    // Only include simple and bundle products (exclude session/package)
                    if (in_array($item->product->type, ['simple', 'bundle']) && $item->product->weight > 0) {
                        // Check if this product has free shipping
                        if ($item->product->free_shipping) {
                            continue; // Skip free shipping items from weight calculation
                        }

                        $allItemsFreeShipping = false;
                        $itemWeight = $item->product->weight * $item->quantity;
                        $totalWeight += $itemWeight;

                        // Add package info for API
                        $validated['packages'][] = [
                            'weight' => $itemWeight,
                            'length' => 20, // Default dimensions
                            'width' => 15,
                            'height' => 10
                        ];
                    }
                }
            }

            // Check if cart has only free shipping items
            $hasOnlyFreeShippingItems = $allItemsFreeShipping && $cart->items->count() > 0;

            // Default to 1kg if no shippable items found (but not free shipping)
            if ($totalWeight == 0 && !$hasOnlyFreeShippingItems) {
                $totalWeight = 1.0;
                $validated['packages'] = [
                    ['weight' => 1.0, 'length' => 20, 'width' => 15, 'height' => 10]
                ];
            }
        } else {
            // Use provided packages
            $totalWeight = collect($validated['packages'] ?? [])->sum('weight');
        }

        $rates = [];

        // Find applicable shipping zones
        $zones = ShippingZone::active()
            ->get()
            ->filter(function ($zone) use ($validated) {
                return $zone->includesAddress($validated['destination']);
            });

        // Prioritize specific country zones over wildcard zones
        // If we have a specific match, remove wildcard zones
        $hasSpecificMatch = $zones->contains(function ($zone) use ($validated) {
            return !in_array('*', $zone->countries) &&
                   in_array($validated['destination']['country'], $zone->countries);
        });

        if ($hasSpecificMatch) {
            $zones = $zones->filter(function ($zone) {
                return !in_array('*', $zone->countries);
            });
        }

        foreach ($zones as $zone) {
            $methods = $zone->shippingMethods()
                ->active()
                ->with(['carrier', 'rates'])
                ->get();

            foreach ($methods as $method) {
                if ($method->rate_source === 'live_api' && 
                    $method->carrier->supports_live_rates) {
                    // Get live rates from carrier API
                    try {
                        $carrierRates = $this->getCarrierRates($method->carrier, $validated);
                        $rates = array_merge($rates, $carrierRates);
                    } catch (\Exception $e) {
                        \Log::error('Live rate calculation failed for ' . $method->carrier->name, [
                            'error' => $e->getMessage(),
                            'carrier' => $method->carrier->slug
                        ]);
                        // Fall back to fixed rate if available
                        $rate = $this->calculateMethodRate($method, $totalWeight, $validated);
                        if ($rate) {
                            $rates[] = $rate;
                        }
                    }
                } else {
                    $rate = $this->calculateMethodRate($method, $totalWeight, $validated);
                    if ($rate) {
                        $rates[] = $rate;
                    }
                }
            }
        }

        // If all items have free shipping, return free shipping option
        if ($hasOnlyFreeShippingItems) {
            $rates = [
                [
                    'method_id' => 'free_shipping',
                    'carrier_id' => null,
                    'carrier_name' => 'Free Shipping',
                    'service_name' => 'Free Shipping',
                    'service_code' => 'FREE',
                    'price' => 0,
                    'currency' => 'EGP',
                    'currency_symbol' => 'EGP',
                    'estimated_days' => 3,
                    'is_free' => true
                ]
            ];
        }

        // Save quote if cart_id provided
        if (isset($validated['cart_id']) && $validated['cart_id']) {
            $this->saveShippingQuote($validated['cart_id'], $validated['destination'], $rates);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'rates' => $rates,
                'has_free_shipping' => $hasOnlyFreeShippingItems
            ]
        ]);
    }

    public function getQuote($cartId): JsonResponse
    {
        $quote = ShippingQuote::where('cart_id', $cartId)
            ->active()
            ->latest()
            ->first();

        if (!$quote) {
            return response()->json([
                'success' => false,
                'message' => 'No active shipping quote found for this cart'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'quote' => $quote
            ]
        ]);
    }

    public function selectMethod(Request $request, $quoteId): JsonResponse
    {
        $validated = $request->validate([
            'method_id' => 'required|integer'
        ]);

        $quote = ShippingQuote::findOrFail($quoteId);

        if ($quote->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping quote has expired'
            ], 422);
        }

        // Verify the method exists in available methods
        $methodExists = collect($quote->available_methods)
            ->contains('method_id', $validated['method_id']);

        if (!$methodExists) {
            return response()->json([
                'success' => false,
                'message' => 'Selected method is not available for this quote'
            ], 422);
        }

        $quote->update([
            'selected_method_id' => $validated['method_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shipping method selected successfully',
            'data' => [
                'quote' => $quote->fresh()
            ]
        ]);
    }

    public function validateAddress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'line1' => 'required|string|max:255',
            'line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'required|string|size:2',
            'postal_code' => 'nullable|string|max:20'
        ]);

        // TODO: Implement actual address validation with carrier APIs
        // For now, return success for properly formatted addresses
        
        return response()->json([
            'success' => true,
            'data' => [
                'is_valid' => true,
                'suggested_address' => $validated,
                'message' => 'Address is valid'
            ]
        ]);
    }

    public function getDeliveryEstimate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'origin' => 'required|array',
            'destination' => 'required|array', 
            'carrier_id' => 'nullable|exists:shipping_carriers,id',
            'method_id' => 'nullable|exists:shipping_methods,id'
        ]);

        // TODO: Implement actual delivery estimation logic
        $estimatedDays = rand(1, 7); // Placeholder

        return response()->json([
            'success' => true,
            'data' => [
                'estimated_days' => $estimatedDays,
                'estimated_delivery_date' => now()->addDays($estimatedDays)->format('Y-m-d')
            ]
        ]);
    }

    public function getAvailableCarriers(): JsonResponse
    {
        $carriers = ShippingCarrier::active()
            ->select(['id', 'name', 'slug', 'supports_tracking', 'supports_labels', 'supports_live_rates'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'carriers' => $carriers
            ]
        ]);
    }

    private function calculateMethodRate($method, $totalWeight, $data): ?array
    {
        $orderAmount = 0; // TODO: Calculate from cart if available

        if (!$method->isAvailableForOrder($orderAmount)) {
            return null;
        }

        $price = 0;

        // Check rate_source to determine how to calculate price
        if ($method->rate_source === 'fixed') {
            // Get price from rates table
            $rate = $method->rates()->first();
            if ($rate && $rate->base_price) {
                $price = $rate->base_price;
            }
        } else {
            // Legacy calculation_type logic
            switch ($method->calculation_type) {
                case ShippingMethod::TYPE_FREE:
                    $price = 0;
                    break;
                    
                case ShippingMethod::TYPE_WEIGHT_BASED:
                    $rate = $method->rates()->first();
                    
                    if (!$rate || !$rate->base_price) {
                        return null;
                    }
                    
                    $price = $rate->base_price;
                    break;
                    
                case ShippingMethod::TYPE_FLAT_RATE:
                    $price = 10.00; // Placeholder
                    break;
            }
        }

        // Fixed rates are now stored directly in EGP
        $currencyCode = 'EGP';
        $currencySymbol = 'EGP';

        return [
            'method_id' => $method->id,
            'carrier_id' => $method->carrier_id,
            'carrier_name' => $method->carrier->name,
            'service_name' => $method->name,
            'service_code' => $method->carrier->slug . '_' . $method->id,
            'price' => round($price, 2),
            'currency' => $currencyCode,
            'currency_symbol' => $currencySymbol,
            'estimated_days' => $method->estimated_days_max,
            'is_free' => $method->is_free || $price == 0
        ];
    }

    private function getCarrierRates($carrier, $data): array
    {
        switch (strtolower($carrier->slug)) {
            case 'aramex':
                if (!$carrier->isConfigured()) {
                    throw new \Exception('Aramex API not configured');
                }
                
                $service = new \App\Services\Shipping\AramexService($carrier->api_config);
                $carrierRates = $service->calculateRates(
                    $data['origin'],
                    $data['destination'],
                    $data['packages']
                );
                
                // Live rates from AramexService are already in EGP
                return collect($carrierRates)->map(function ($rate) use ($carrier) {
                    return [
                        'method_id' => 'live_' . $carrier->id . '_' . $rate['service_code'],
                        'carrier_id' => $carrier->id,
                        'carrier_name' => $rate['carrier_name'],
                        'service_name' => $rate['service_name'],
                        'service_code' => $rate['service_code'],
                        'price' => round($rate['price'], 2),
                        'currency' => $rate['currency'],
                        'currency_symbol' => $rate['currency'],
                        'estimated_days' => $rate['estimated_days'],
                        'is_free' => false
                    ];
                })->toArray();
                
            case 'dhl':
            case 'fedex':
                // TODO: Implement other carriers
                throw new \Exception(ucfirst($carrier->slug) . ' live rates not implemented yet');
                
            default:
                throw new \Exception('Live rates not supported for ' . $carrier->name);
        }
    }

    private function saveShippingQuote($cartId, $destination, $rates): void
    {
        // Delete existing quotes for this cart
        ShippingQuote::where('cart_id', $cartId)->delete();

        ShippingQuote::create([
            'cart_id' => $cartId,
            'destination_address' => $destination,
            'available_methods' => $rates,
            'expires_at' => now()->addHour()
        ]);
    }
}