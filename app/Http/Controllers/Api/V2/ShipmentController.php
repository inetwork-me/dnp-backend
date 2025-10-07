<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\Order;
use App\Models\ShippingCarrier;
use App\Services\Shipping\AramexService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class ShipmentController extends Controller
{
    public function list(Request $request): JsonResponse
    {
        $query = Shipment::with(['order', 'carrier'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('carrier_id')) {
            $query->where('carrier_id', $request->carrier_id);
        }

        if ($request->has('tracking_number')) {
            $query->where('tracking_number', 'like', '%' . $request->tracking_number . '%');
        }

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        $shipments = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'shipments' => $shipments->items(),
                'pagination' => [
                    'current_page' => $shipments->currentPage(),
                    'last_page' => $shipments->lastPage(),
                    'per_page' => $shipments->perPage(),
                    'total' => $shipments->total()
                ]
            ]
        ]);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'shipment_data' => 'required|array',
            'shipment_data.origin' => 'required|array',
            'shipment_data.weight' => 'required|numeric|min:0.1',
            'shipment_data.description' => 'nullable|string',
            'shipment_data.reference' => 'nullable|string'
        ]);

        try {
            $order = Order::with('user')->findOrFail($validated['order_id']);

            // Check if order already has shipment
            if ($order->shipments()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order already has a shipment'
                ], 422);
            }

            // Get Aramex carrier
            $carrier = ShippingCarrier::where('slug', 'aramex')->firstOrFail();

            // Initialize Aramex service
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

            // Prepare shipment data
            $shipmentData = [
                'reference' => $validated['shipment_data']['reference'] ?? 'ORD-' . $order->id,
                'origin' => $validated['shipment_data']['origin'],
                'destination' => [
                    'line1' => $order->shipping_address['line1'] ?? '',
                    'line2' => $order->shipping_address['line2'] ?? '',
                    'city' => $order->shipping_address['city'] ?? '',
                    'country' => $order->shipping_address['country'] ?? 'EG',
                    'postal_code' => $order->shipping_address['postal_code'] ?? ''
                ],
                'packages' => [['weight' => $validated['shipment_data']['weight']]],
                'weight' => $validated['shipment_data']['weight'],
                'description' => $validated['shipment_data']['description'] ?? 'Order items',
                'product_group' => $validated['shipment_data']['origin']['country'] === ($order->shipping_address['country'] ?? 'EG') ? 'DOM' : 'EXP',
                'product_type' => $validated['shipment_data']['origin']['country'] === ($order->shipping_address['country'] ?? 'EG') ? 'CDS' : 'PDX',
                'shipper_name' => config('app.name', 'DNP Store'),
                'shipper_company' => config('app.name', 'DNP Store'),
                'shipper_phone' => config('shipping.aramex.shipper_phone'),
                'shipper_email' => config('shipping.aramex.shipper_email'),
                'consignee_name' => $order->user->name,
                'consignee_company' => $order->user->company_name ?? '',
                'consignee_phone' => $order->shipping_address['phone'] ?? $order->user->phone,
                'consignee_email' => $order->user->email
            ];

            // Create shipment via Aramex API
            $result = $aramexService->createShipment($shipmentData);

            if (!$result['success']) {
                throw new Exception($result['error'] ?? 'Failed to create shipment');
            }

            // Save shipment to database
            $shipment = Shipment::create([
                'order_id' => $order->id,
                'carrier_id' => $carrier->id,
                'tracking_number' => $result['tracking_number'],
                'status' => Shipment::STATUS_BOOKED,
                'carrier_response' => $result['carrier_response'],
                'shipped_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Shipment created successfully',
                'data' => [
                    'shipment' => $shipment->load(['order', 'carrier']),
                    'tracking_number' => $result['tracking_number']
                ]
            ], 201);

        } catch (Exception $e) {
            Log::error('Shipment creation failed', [
                'order_id' => $validated['order_id'],
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create shipment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($shipmentId): JsonResponse
    {
        $shipment = Shipment::with(['order.user', 'carrier'])
            ->findOrFail($shipmentId);

        // Get tracking info with events
        $trackingInfo = $this->getTrackingInfo($shipment);

        return response()->json([
            'success' => true,
            'data' => [
                'shipment' => $shipment,
                'tracking_events' => $trackingInfo['events'] ?? [],
                'estimated_delivery_date' => $this->calculateEstimatedDelivery($shipment),
                'origin_address' => [
                    'name' => 'DNP Store Warehouse',
                    'line1' => 'Cairo Distribution Center',
                    'city' => 'Cairo',
                    'country' => 'EG'
                ],
                'destination_address' => [
                    'name' => $shipment->order->user->name ?? null,
                    'line1' => $shipment->order->shipping_address['line1'] ?? null,
                    'city' => $shipment->order->shipping_address['city'] ?? null,
                    'country' => $shipment->order->shipping_address['country'] ?? null
                ]
            ]
        ]);
    }

    public function track($shipmentId): JsonResponse
    {
        try {
            $shipment = Shipment::with(['order', 'carrier'])->findOrFail($shipmentId);

            if ($shipment->carrier->slug !== 'aramex') {
                return response()->json([
                    'success' => false,
                    'message' => 'Real-time tracking only supported for Aramex shipments'
                ], 422);
            }

            // Initialize Aramex service
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
            $result = $aramexService->trackShipments($shipment->tracking_number, false);

            return response()->json([
                'success' => true,
                'data' => [
                    'shipment' => $shipment,
                    'tracking_results' => $result['tracking_results'],
                    'carrier_response' => $result['carrier_response']
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Shipment tracking failed', [
                'shipment_id' => $shipmentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to track shipment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function trackByNumber($trackingNumber): JsonResponse
    {
        $shipment = Shipment::with(['order', 'carrier'])
            ->where('tracking_number', $trackingNumber)
            ->first();

        if (!$shipment) {
            return response()->json([
                'success' => false,
                'message' => 'Shipment not found with this tracking number'
            ], 404);
        }

        $trackingInfo = $this->getTrackingInfo($shipment);

        return response()->json([
            'success' => true,
            'data' => [
                'shipment' => $shipment,
                'tracking_info' => $trackingInfo
            ]
        ]);
    }

    public function printLabel($shipmentId): JsonResponse
    {
        try {
            $shipment = Shipment::with('carrier')->findOrFail($shipmentId);

            if ($shipment->carrier->slug !== 'aramex') {
                return response()->json([
                    'success' => false,
                    'message' => 'Label printing only supported for Aramex shipments'
                ], 422);
            }

            // Initialize Aramex service
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

            // Get product group from carrier_response
            $productGroup = $shipment->carrier_response['Shipments'][0]['Details']['ProductGroup'] ?? 'DOM';
            $originEntity = config('shipping.aramex.account_entity', 'CAI');

            $result = $aramexService->printLabel($shipment->tracking_number, $originEntity, $productGroup);

            // Update shipment with label info
            $shipment->update([
                'shipping_label' => [
                    'label_url' => $result['label_url'],
                    'label_file_contents' => $result['label_file_contents'],
                    'generated_at' => now()
                ]
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'label_url' => $result['label_url'],
                    'label_file_contents' => $result['label_file_contents']
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Label printing failed', [
                'shipment_id' => $shipmentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to print label: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, $shipmentId): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,booked,picked_up,in_transit,delivered,cancelled'
        ]);

        $shipment = Shipment::findOrFail($shipmentId);

        // Update status
        $shipment->update([
            'status' => $validated['status'],
            'shipped_at' => $validated['status'] === Shipment::STATUS_PICKED_UP ? now() : $shipment->shipped_at,
            'delivered_at' => $validated['status'] === Shipment::STATUS_DELIVERED ? now() : $shipment->delivered_at
        ]);

        // TODO: Send notification to customer about status change

        $shipment->load(['order', 'carrier']);

        return response()->json([
            'success' => true,
            'message' => 'Shipment status updated successfully',
            'data' => [
                'shipment' => $shipment
            ]
        ]);
    }

    private function generateTrackingNumber($carrier): string
    {
        // Generate a unique tracking number
        // TODO: Use carrier-specific format
        return strtoupper($carrier->slug) . '-' . now()->format('Ymd') . '-' . rand(100000, 999999);
    }

    private function getTrackingInfo($shipment): array
    {
        // TODO: Integrate with actual carrier tracking APIs
        // For now, return mock tracking info
        
        $events = [
            [
                'status' => 'Label Created',
                'description' => 'Shipping label has been created',
                'location' => 'Origin',
                'timestamp' => $shipment->created_at
            ]
        ];

        if ($shipment->status !== Shipment::STATUS_PENDING) {
            $events[] = [
                'status' => 'Package Booked',
                'description' => 'Package has been booked with carrier',
                'location' => 'Origin',
                'timestamp' => $shipment->created_at->addMinutes(30)
            ];
        }

        if (in_array($shipment->status, [Shipment::STATUS_PICKED_UP, Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_DELIVERED])) {
            $events[] = [
                'status' => 'Package Picked Up',
                'description' => 'Package has been picked up by carrier',
                'location' => 'Origin',
                'timestamp' => $shipment->shipped_at ?: $shipment->created_at->addHours(2)
            ];
        }

        if (in_array($shipment->status, [Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_DELIVERED])) {
            $events[] = [
                'status' => 'In Transit',
                'description' => 'Package is in transit',
                'location' => 'Sorting Facility',
                'timestamp' => $shipment->shipped_at ? $shipment->shipped_at->addHours(4) : $shipment->created_at->addHours(6)
            ];
        }

        if ($shipment->status === Shipment::STATUS_DELIVERED) {
            $events[] = [
                'status' => 'Delivered',
                'description' => 'Package has been delivered',
                'location' => 'Destination',
                'timestamp' => $shipment->delivered_at ?: now()
            ];
        }

        return [
            'tracking_number' => $shipment->tracking_number,
            'status' => $shipment->status,
            'carrier_name' => $shipment->carrier->name,
            'events' => array_reverse($events) // Show latest first
        ];
    }

    private function generateShippingLabel($shipment): string
    {
        // TODO: Generate actual shipping label via carrier API
        // For now, return a placeholder URL
        return url('/storage/labels/' . $shipment->tracking_number . '.pdf');
    }

    private function calculateEstimatedDelivery($shipment): ?string
    {
        if ($shipment->status === Shipment::STATUS_DELIVERED) {
            return null; // Already delivered
        }

        // Calculate estimated delivery based on creation date and typical delivery time
        $estimatedDays = 3; // Default 3 days for domestic shipping
        
        return $shipment->created_at->addDays($estimatedDays)->format('Y-m-d');
    }
}