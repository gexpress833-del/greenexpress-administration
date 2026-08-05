<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderForCuisinier extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['app'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Nouvelle commande à préparer',
            'message' => 'La commande '.$this->order->code.' de '.($this->order->client_name ?? 'un client').' a été validée et est à préparer.',
            'type' => 'new_order_for_cuisinier',
            'category' => 'order',
            'url' => route('cuisinier.orders.show', $this->order),
        ];
    }
}
