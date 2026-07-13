<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class DailyDeliveryCode extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): DatabaseMessage
    {
        $meal = $this->order->items->first()?->meal?->name ?? 'Non défini';
        $deliveryDate = $this->order->delivery_date?->format('d/m/Y') ?? 'Date non définie';

        return new DatabaseMessage([
            'title' => 'Code de validation pour votre repas du jour',
            'message' => "Votre repas du {$deliveryDate} est : {$meal}. Code de validation : {$this->order->client_validation_code}. Remettez ce code au livreur uniquement après réception.",
            'order_id' => $this->order->id,
            'code' => $this->order->client_validation_code,
            'url' => route('client.dashboard'),
            'icon' => 'check-circle',
            'color' => 'green',
            'type' => 'daily_delivery_code',
        ]);
    }
}
