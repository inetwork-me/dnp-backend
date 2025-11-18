<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Order;

class GuestUserWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $password;
    protected $order;

    public function __construct(User $user, string $password, Order $order)
    {
        $this->user = $user;
        $this->password = $password;
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . ' - Your Account Created')
            ->greeting('Welcome to ' . config('app.name') . '!')
            ->line('Thank you for your purchase! We have created an account for you to track your orders and shop easier in the future.')
            ->line('**Your Account Details:**')
            ->line('Email: ' . $this->user->email)
            ->line('Password: ' . $this->password)
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Order Total: ' . $this->order->formatted_total . ' (stored in EGP: ' . number_format($this->order->total_amount, 2) . ')')
            ->action('View Your Order', config('app.frontend_url') . '/my-account/my-orders')
            ->line('You can now log in to your account anytime to:')
            ->line('• Track your orders')
            ->line('• View order history')
            ->line('• Update your profile')
            ->line('• Shop faster with saved information')
            ->line('We recommend changing your password after your first login for security.')
            ->line('Thank you for choosing ' . config('app.name') . '!');
    }

    public function toArray($notifiable)
    {
        return [
            'user_id' => $this->user->id,
            'order_id' => $this->order->id,
            'message' => 'Guest account created for order ' . $this->order->order_number
        ];
    }
}