<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Shipment;
use App\Models\Order;
use App\Models\ShippingCarrier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
            'carrier_id' => 'required|exists:shipping_carriers,id',
            'shipment_data' => 'required|array',
            'shipment_data.origin' => 'required|array',
            'shipment_data.destination' => 'required|array',
            'shipment_data.packages' => 'required|array|min:1',
            'shipment_data.reference' => 'nullable|string|max:255'
        ]);

        // Check if order already has a shipment
        $existingShipment = Shipment::where('order_id', $validated['order_id'])->first();
        if ($existingShipment) {
            return response()->json([
                'success' => false,
                'message' => 'Order already has a shipment'
            ], 422);
        }

        $order = Order::findOrFail($validated['order_id']);
        $carrier = ShippingCarrier::findOrFail($validated['carrier_id']);

        // TODO: Integrate with actual carrier API to create shipment
        $trackingNumber = $this->generateTrackingNumber($carrier);
        
        $shipment = Shipment::create([
            'order_id' => $validated['order_id'],
            'carrier_id' => $validated['carrier_id'],
            'tracking_number' => $trackingNumber,
            'status' => Shipment::STATUS_PENDING,
            'carrier_response' => [
                'created_at' => now(),
                'shipment_data' => $validated['shipment_data']
            ]
        ]);

        $shipment->load(['order', 'carrier']);

        return response()->json([
            'success' => true,
            'message' => 'Shipment created successfully',
            'data' => [
                'shipment' => $shipment
            ]
        ], 201);
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
        $shipment = Shipment::with(['order', 'carrier'])
            ->findOrFail($shipmentId);

        // TODO: Integrate with carrier API for real-time tracking
        $trackingInfo = $this->getTrackingInfo($shipment);

        return response()->json([
            'success' => true,
            'data' => [
                'shipment' => $shipment,
                'tracking_info' => $trackingInfo
            ]
        ]);
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

    public function getLabel($shipmentId): JsonResponse
    {
        $shipment = Shipment::findOrFail($shipmentId);

        if (!$shipment->carrier->supports_labels) {
            return response()->json([
                'success' => false,
                'message' => 'Carrier does not support shipping labels'
            ], 422);
        }

        // TODO: Integrate with carrier API to get label
        $labelUrl = $this->generateShippingLabel($shipment);

        return response()->json([
            'success' => true,
            'data' => [
                'label_url' => $labelUrl,
                'format' => 'PDF'
            ]
        ]);
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