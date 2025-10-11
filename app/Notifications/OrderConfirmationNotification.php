<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $order = $this->order;

        // Format order items for email
        $itemsText = '';
        foreach ($order->items as $item) {
            $productName = $item->product ? $item->product->getTranslation('name') : 'Product';
            $itemsText .= '• ' . $productName . ' x' . $item->quantity . ' - ' . number_format($item->line_total, 2) . ' EGP' . "\n";
        }

        $mailMessage = (new MailMessage)
            ->subject('Order Confirmation - ' . $order->order_number)
            ->greeting('Hello ' . ($notifiable->name ?? 'Customer') . '!')
            ->line('Thank you for your order! Your order has been received and is being processed.')
            ->line('**Order Details:**')
            ->line('Order Number: **' . $order->order_number . '**')
            ->line('Order Date: ' . $order->created_at->format('F j, Y'))
            ->line('Payment Method: ' . ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')))
            ->line('Payment Status: ' . ucfirst($order->payment_status ?? 'Pending'))
            ->line('')
            ->line('**Items Ordered:**')
            ->line($itemsText)
            ->line('**Order Summary:**')
            ->line('Subtotal: ' . number_format($order->subtotal, 2) . ' EGP');

        // Add discount if applicable
        if ($order->discount > 0) {
            $mailMessage->line('Discount: -' . number_format($order->discount, 2) . ' EGP');
        }

        // Add shipping cost if applicable
        if ($order->shipping_cost > 0) {
            $mailMessage->line('Shipping: ' . number_format($order->shipping_cost, 2) . ' EGP');
        }

        $mailMessage->line('**Total: ' . number_format($order->total_amount, 2) . ' EGP**')
            ->line('')
            ->action('View Your Order', config('app.frontend_url') . '/my-account/my-orders')
            ->line('If you have any questions about your order, please contact our customer support.')
            ->line('Thank you for shopping with ' . config('app.name') . '!');

        return $mailMessage;
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'message' => 'Order confirmation for ' . $this->order->order_number
        ];
    }
}
