<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Product;

class LowStockAlert extends Notification
{
    use Queueable;

    protected Product $product;
    protected int $quantity;

    public function __construct(Product $product, int $quantity)
    {
        $this->product = $product;
        $this->quantity = $quantity;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url('/products/' . $this->product->id);

        return (new MailMessage)
            ->subject('Low stock alert: ' . $this->product->nom)
            ->greeting('Hello,')
            ->line('Product "' . $this->product->nom . '" has low stock.')
            ->line('Current quantity: ' . $this->quantity)
            ->line('Threshold: ' . $this->product->stock_threshold)
            ->action('View product', $url)
            ->line('Please replenish stock if necessary.');
    }
}
