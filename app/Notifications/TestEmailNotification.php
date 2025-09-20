<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TestEmailNotification extends Notification
{
    use Queueable;

    protected $testData;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $testData = [])
    {
        $this->testData = $testData;
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
        $orderNumber = $this->testData['order_number'] ?? 'TEST-' . now()->format('YmdHis');
        $customerName = $this->testData['customer_name'] ?? 'Test Customer';
        $total = $this->testData['total'] ?? '150.00';

        return (new MailMessage)
            ->subject('🧪 Test Email - New Order Notification')
            ->line('This is a test email from your e-commerce system.')
            ->line("**Order Details:**")
            ->line("Order Number: **{$orderNumber}**")
            ->line("Customer: **{$customerName}**")
            ->line("Total Amount: **\${$total}**")
            ->line('This test email confirms that your email notification system is working correctly.')
            ->line('You can safely ignore this message as it was sent for testing purposes.')
            ->line('Thank you for using our e-commerce platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'test_email',
            'data' => $this->testData,
        ];
    }
}