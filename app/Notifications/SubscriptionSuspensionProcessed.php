<?php

namespace App\Notifications;

use App\Models\SubscriptionSuspension;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionSuspensionProcessed extends Notification
{
    use Queueable;

    public function __construct(public SubscriptionSuspension $suspension) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $accepted = $this->suspension->status === 'accepted';
        $statusText = $accepted ? 'acceptée' : 'rejetée';

        return [
            'title' => 'Suspension '.$statusText,
            'message' => "Votre demande de suspension a été {$statusText} par l'administrateur.",
            'subscription_id' => $this->suspension->subscription_id,
            'url' => route('client.subscriptions.show', $this->suspension->subscription_id),
            'icon' => $accepted ? 'check-circle' : 'x-circle',
            'color' => $accepted ? 'green' : 'red',
        ];
    }
}
