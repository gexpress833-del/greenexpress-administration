<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DeliveryValidated extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $validatedBy) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Livraison validée',
            'message' => "La commande {$this->order->code} a été validée par {$this->validatedBy}.",
            'order_id' => $this->order->id,
            'url' => $notifiable->isAdmin()
                ? route('admin.orders.show', $this->order)
                : route('agent.orders.show', $this->order),
            'icon' => 'check-circle',
            'color' => 'green',
        ];
    }
}
