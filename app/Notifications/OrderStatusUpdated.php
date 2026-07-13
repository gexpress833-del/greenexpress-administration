<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $oldStatus) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $statusLabels = [
            'pending' => 'en attente',
            'confirmed' => 'validée',
            'preparing' => 'en préparation',
            'delivering' => 'en livraison',
            'delivered' => 'livrée',
            'cancelled' => 'annulée',
        ];

        $label = $statusLabels[$this->order->status] ?? $this->order->status;

        return [
            'title' => 'Mise à jour de commande',
            'message' => "Votre commande {$this->order->code} est maintenant {$label}.",
            'order_id' => $this->order->id,
            'url' => route('agent.orders.show', $this->order),
            'icon' => 'check-circle',
            'color' => $this->order->status === 'cancelled' ? 'red' : 'green',
        ];
    }
}
