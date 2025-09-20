<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class NewOrderAdminNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order;

        return (new MailMessage)
            ->subject('New Order Received - ' . $order->order_number)
            ->greeting('New Order Alert!')
            ->line('A new order has been placed on your store.')
            ->line('**Order Details:**')
            ->line('Order Number: ' . $order->order_number)
            ->line('Customer: ' . $order->user->name)
            ->line('Email: ' . $order->user->email)
            ->line('Total Amount: ' . number_format($order->total_amount, 2) . ' EGP')
            ->line('Items: ' . $order->items->count() . ' item(s)')
            ->line('Status: ' . ucfirst($order->status))
            ->action('View Order in Admin', url('/admin/orders/' . $order->id))
            ->line('Please process this order as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total_amount' => $this->order->total_amount,
            'customer_name' => $this->order->user->name,
        ];
    }
}
