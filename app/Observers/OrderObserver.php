<?php
// app/Observers/OrderObserver.php
namespace App\Observers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Auth;

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
        }
    }
}
