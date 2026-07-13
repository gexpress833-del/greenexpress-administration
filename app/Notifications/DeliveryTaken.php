<?php

namespace App\Notifications;

use App\Models\Delivery;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeliveryTaken extends Notification
{
    use Queueable;

    public function __construct(public Delivery $delivery) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $livreurName = $this->delivery->livreur->name ?? 'Livreur';
        $orderCode = $this->delivery->order->code ?? 'Commande';

        return [
            'title' => 'Livraison prise en charge',
            'message' => "{$livreurName} a pris en charge la livraison de la commande {$orderCode}.",
            'delivery_id' => $this->delivery->id,
            'url' => route('admin.orders.show', $this->delivery->order),
            'icon' => 'truck',
            'color' => 'purple',
        ];
    }
}
