<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderCreated extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Nouvelle commande',
            'message' => "L'agent {$this->order->agent->name} a créé la commande {$this->order->code}.",
            'order_id' => $this->order->id,
            'url' => route('admin.orders.show', $this->order),
            'icon' => 'shopping-cart',
            'color' => 'blue',
        ];
    }
}
