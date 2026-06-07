<?php

namespace App\Notifications;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeliveryAssigned extends Notification
{
    use Queueable;

    public function __construct(public Delivery $delivery) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Livraison en cours',
            'message' => "Le livreur {$this->delivery->livreur->name} a pris en charge votre commande {$this->delivery->order->code}.",
            'order_id' => $this->delivery->order_id,
            'url' => route('agent.orders.show', $this->delivery->order),
            'icon' => 'truck',
            'color' => 'purple',
        ];
    }
}
