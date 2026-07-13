<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class SubscriptionDeliveriesAvailable extends Notification
{
    use Queueable;

    public function __construct(
        public int $todayCount,
        public int $totalCount,
        public string $clientName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $message = "Nouvelles livraisons disponibles pour {$this->clientName}. ";
        $message .= $this->todayCount > 0
            ? "{$this->todayCount} livraison(s) aujourd'hui, {$this->totalCount} au total."
            : "{$this->totalCount} livraisons au total.";

        return new DatabaseMessage([
            'title' => 'Nouvelles livraisons disponibles',
            'message' => $message,
            'url' => route('livreur.deliveries.index'),
            'icon' => 'truck',
            'color' => 'green',
            'type' => 'subscription_deliveries_available',
        ]);
    }
}
