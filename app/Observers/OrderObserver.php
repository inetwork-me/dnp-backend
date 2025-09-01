<?php
// app/Observers/OrderObserver.php
namespace App\Observers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Shipment;
use App\Models\ShippingCarrier;
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
            $carrier = ShippingCarrier::active()->first();
            
            if (!$carrier) {
                Log::warning('No active shipping carrier found for order', ['order_id' => $order->id]);
                return;
            }

            // Generate tracking number
            $trackingNumber = $this->generateTrackingNumber($carrier);

            // Create shipment
            $shipment = Shipment::create([
                'order_id' => $order->id,
                'carrier_id' => $carrier->id,
                'tracking_number' => $trackingNumber,
                'status' => Shipment::STATUS_PENDING,
                'carrier_response' => [
                    'auto_created' => true,
                    'created_at' => now(),
                    'carrier' => $carrier->name
                ]
            ]);

            Log::info('Auto-created shipment for order', [
                'order_id' => $order->id,
                'shipment_id' => $shipment->id,
                'tracking_number' => $trackingNumber,
                'carrier' => $carrier->name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to auto-create shipment for order', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
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
