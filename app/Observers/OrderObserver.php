<?php
// app/Observers/OrderObserver.php
namespace App\Observers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Shipment;
use App\Models\ShippingCarrier;
use App\Services\Shipping\AramexService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function updating(Order $order)
    {
        if ($order->isDirty('status')) {
            OrderStatusHistory::create([
                'order_id'   => $order->id,
                'old_status' => $order->getOriginal('status'),
                'new_status' => $order->status,
                'user_id'    => Auth::id(),
            ]);

            // Auto-create shipment for confirmed orders
            $this->createShipmentIfNeeded($order);
        }
    }

    public function created(Order $order)
    {
        // Auto-create shipment for orders that are created as confirmed
        if (in_array($order->status, ['confirmed', 'processing'])) {
            $this->createShipmentIfNeeded($order);
        }
    }

    private function createShipmentIfNeeded(Order $order)
    {
        try {
            if (!$order->shouldCreateShipment()) {
                return;
            }

            // Get default shipping carrier (Aramex for now)
            $carrier = ShippingCarrier::where('slug', 'aramex')->active()->first();

            if (!$carrier) {
                Log::warning('No active Aramex carrier found for order', ['order_id' => $order->id]);
                return;
            }

            // Load order with relationships for shipment creation
            $order->load('user');

            if (!$order->shipping_address) {
                Log::warning('No shipping address found for order', ['order_id' => $order->id]);
                return;
            }

            $shippingAddress = $order->shipping_address; // JSON field

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
                'shipper' => [
                    'name' => config('app.name'),
                    'email' => config('shipping.aramex.shipper_email'),
                    'phone' => config('shipping.aramex.shipper_phone'),
                    'line1' => 'Cairo Distribution Center',
                    'city' => 'Cairo',
                    'country' => 'EG'
                ],
                'consignee' => [
                    'name' => $order->user->name ?? 'Customer',
                    'email' => $order->user->email ?? '',
                    'phone' => $order->user->phone ?? '',
                    'line1' => $shippingAddress['line1'] ?? '',
                    'line2' => $shippingAddress['line2'] ?? '',
                    'city' => $shippingAddress['city'] ?? '',
                    'state' => $shippingAddress['state'] ?? '',
                    'country' => $shippingAddress['country'] ?? 'EG',
                    'postal_code' => $shippingAddress['postal_code'] ?? ''
                ],
                'shipment_details' => [
                    'number_of_pieces' => 1,
                    'description' => 'Order #' . $order->order_number,
                    'weight' => 1.0, // Default 1kg, adjust as needed
                    'payment_type' => 'P', // Prepaid
                    'product_group' => ($shippingAddress['country'] ?? 'EG') === 'EG' ? 'DOM' : 'EXP',
                    'product_type' => ($shippingAddress['country'] ?? 'EG') === 'EG' ? 'CDS' : 'PDX'
                ],
                'reference' => $order->order_number
            ];

            // Create shipment via Aramex API
            $result = $aramexService->createShipment($shipmentData);

            if (!$result['success']) {
                Log::error('Aramex API failed to create shipment', [
                    'order_id' => $order->id,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
                return;
            }

            // Create shipment record with Aramex response
            $shipment = Shipment::create([
                'order_id' => $order->id,
                'carrier_id' => $carrier->id,
                'tracking_number' => $result['tracking_number'],
                'status' => Shipment::STATUS_BOOKED,
                'carrier_response' => $result['carrier_response'],
                'shipped_at' => now()
            ]);

            Log::info('Auto-created shipment via Aramex API', [
                'order_id' => $order->id,
                'shipment_id' => $shipment->id,
                'tracking_number' => $result['tracking_number']
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to auto-create shipment for order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function generateTrackingNumber($carrier): string
    {
        $prefix = strtoupper($carrier->slug);
        $date = now()->format('Ymd');
        $random = rand(100000, 999999);
        
        return "{$prefix}-{$date}-{$random}";
    }
}
