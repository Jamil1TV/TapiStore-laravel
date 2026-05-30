<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Order '.$this->order->order_number.' received')
            ->greeting('Thanks, '.$notifiable->name.'!')
            ->line('We received your order and are preparing it for fulfillment.')
            ->line('Order total: '.$this->order->currency.' '.number_format((float) $this->order->total, 2))
            ->action('View order', route('dashboard'))
            ->line('You will receive another update when the order status changes.');
    }
}
