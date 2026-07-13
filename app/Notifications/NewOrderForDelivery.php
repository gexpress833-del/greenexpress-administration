<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class NewOrderForDelivery extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'title' => 'Nouvelle livraison disponible',
            'message' => "La commande {$this->order->code} a été validée par l'administration. Vous pouvez la prendre en charge si elle est encore disponible.",
            'order_id' => $this->order->id,
            'code' => $this->order->code,
            'url' => route('livreur.deliveries.index'),
            'icon' => 'truck',
            'color' => 'green',
            'type' => 'new_order_for_delivery',
        ]);
    }
}
