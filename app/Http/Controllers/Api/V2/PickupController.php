<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Pickup;
use App\Services\Shipping\AramexService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class PickupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Pickup::with('vendor')->orderBy('pickup_date', 'desc');
        if ($request->has('status')) $query->where('status', $request->status);
        if ($request->has('vendor_id')) $query->where('vendor_id', $request->vendor_id);
        if ($request->has('upcoming')) $query->upcoming();
        $pickups = $query->paginate($request->get('per_page', 15));
        return response()->json(['success' => true, 'data' => ['pickups' => $pickups->items(), 'pagination' => ['current_page' => $pickups->currentPage(), 'last_page' => $pickups->lastPage(), 'per_page' => $pickups->perPage(), 'total' => $pickups->total()]]]);
    }
    
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'nullable|exists:users,id',
            'pickup_address_line1' => 'required|string|max:255',
            'pickup_address_line2' => 'nullable|string|max:255',
            'pickup_city' => 'required|string|max:100',
            'pickup_country' => 'required|string|size:2',
            'pickup_postal_code' => 'nullable|string|max:20',
            'contact_person' => 'required|string|max:100',
            'contact_company' => 'nullable|string|max:100',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:100',
            'pickup_date' => 'required|date|after:now',
            'ready_time' => 'required|date_format:H:i',
            'last_pickup_time' => 'required|date_format:H:i|after:ready_time',
            'closing_time' => 'nullable|date_format:H:i',
            'pickup_location' => 'nullable|string|max:100',
            'number_of_shipments' => 'required|integer|min:1',
            'total_weight' => 'required|numeric|min:0.1',
            'comments' => 'nullable|string|max:500'
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

            $pickupDate = $validated['pickup_date'];
            $pickupData = [
                'address' => [
                    'line1' => $validated['pickup_address_line1'],
                    'line2' => $validated['pickup_address_line2'] ?? '',
                    'city' => $validated['pickup_city'],
                    'country' => $validated['pickup_country'],
                    'postal_code' => $validated['pickup_postal_code'] ?? ''
                ],
                'contact' => [
                    'person_name' => $validated['contact_person'],
                    'company_name' => $validated['contact_company'] ?? '',
                    'phone' => $validated['contact_phone'],
                    'email' => $validated['contact_email'] ?? ''
                ],
                'pickup_location' => $validated['pickup_location'] ?? 'Reception',
                'pickup_date' => $pickupDate,
                'ready_time' => $pickupDate . ' ' . $validated['ready_time'],
                'last_pickup_time' => $pickupDate . ' ' . $validated['last_pickup_time'],
                'closing_time' => $pickupDate . ' ' . ($validated['closing_time'] ?? $validated['last_pickup_time']),
                'comments' => $validated['comments'] ?? '',
                'reference' => 'PKP-' . now()->format('YmdHis'),
                'pickup_items' => [[
                    'ProductGroup' => $validated['pickup_country'] === 'EG' ? 'DOM' : 'EXP',
                    'ProductType' => $validated['pickup_country'] === 'EG' ? 'CDS' : 'PDX',
                    'NumberOfShipments' => $validated['number_of_shipments'],
                    'PackageType' => 'Box',
                    'Payment' => 'P',
                    'ShipmentWeight' => ['Unit' => 'KG', 'Value' => $validated['total_weight']],
                    'ShipmentVolume' => null,
                    'NumberOfPieces' => $validated['number_of_shipments'],
                    'CashAmount' => null,
                    'ExtraCharges' => null,
                    'ShipmentDimensions' => ['Length' => 0, 'Width' => 0, 'Height' => 0, 'Unit' => 'CM'],
                    'Comments' => $validated['comments'] ?? ''
                ]],
                'shipments' => []
            ];

            $result = $aramexService->createPickup($pickupData);
            if (!$result['success']) throw new Exception($result['error'] ?? 'Failed to create pickup');

            $pickup = Pickup::create([
                'vendor_id' => $validated['vendor_id'] ?? null,
                'pickup_guid' => $result['pickup_guid'],
                'reference_number' => $result['pickup_reference'],
                'status' => Pickup::STATUS_SCHEDULED,
                'pickup_address_line1' => $validated['pickup_address_line1'],
                'pickup_address_line2' => $validated['pickup_address_line2'],
                'pickup_city' => $validated['pickup_city'],
                'pickup_country' => $validated['pickup_country'],
                'pickup_postal_code' => $validated['pickup_postal_code'],
                'contact_person' => $validated['contact_person'],
                'contact_company' => $validated['contact_company'],
                'contact_phone' => $validated['contact_phone'],
                'contact_email' => $validated['contact_email'],
                'pickup_date' => $validated['pickup_date'],
                'ready_time' => $validated['ready_time'],
                'last_pickup_time' => $validated['last_pickup_time'],
                'closing_time' => $validated['closing_time'],
                'pickup_location' => $validated['pickup_location'],
                'number_of_shipments' => $validated['number_of_shipments'],
                'total_weight' => $validated['total_weight'],
                'comments' => $validated['comments'],
                'carrier_response' => $result['carrier_response'],
                'scheduled_at' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Pickup scheduled successfully', 'data' => ['pickup' => $pickup->load('vendor')]], 201);
        } catch (Exception $e) {
            Log::error('Pickup creation failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to schedule pickup: ' . $e->getMessage()], 500);
        }
    }

    public function show($pickupId): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ['pickup' => Pickup::with('vendor')->findOrFail($pickupId)]]);
    }

    public function track($pickupId): JsonResponse
    {
        try {
            $pickup = Pickup::findOrFail($pickupId);
            if (!$pickup->pickup_guid) {
                return response()->json(['success' => false, 'message' => 'Pickup GUID not available for tracking'], 422);
            }

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
            $result = $aramexService->trackPickup($pickup->pickup_guid);

            return response()->json(['success' => true, 'data' => ['pickup' => $pickup, 'tracking_info' => $result['pickup_info'], 'carrier_response' => $result['carrier_response']]]);
        } catch (Exception $e) {
            Log::error('Pickup tracking failed', ['pickup_id' => $pickupId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to track pickup: ' . $e->getMessage()], 500);
        }
    }

    public function cancel(Request $request, $pickupId): JsonResponse
    {
        $validated = $request->validate(['comments' => 'nullable|string|max:500']);

        try {
            $pickup = Pickup::findOrFail($pickupId);
            if (!$pickup->canBeCancelled()) {
                return response()->json(['success' => false, 'message' => 'Pickup cannot be cancelled in current status: ' . $pickup->status], 422);
            }

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
            $aramexService->cancelPickup($pickup->pickup_guid, $validated['comments'] ?? '');

            $pickup->update([
                'status' => Pickup::STATUS_CANCELLED,
                'cancelled_at' => now(),
                'comments' => ($pickup->comments ?? '') . "\n[CANCELLED] " . ($validated['comments'] ?? 'No reason provided')
            ]);

            return response()->json(['success' => true, 'message' => 'Pickup cancelled successfully', 'data' => ['pickup' => $pickup]]);
        } catch (Exception $e) {
            Log::error('Pickup cancellation failed', ['pickup_id' => $pickupId, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to cancel pickup: ' . $e->getMessage()], 500);
        }
    }
}
