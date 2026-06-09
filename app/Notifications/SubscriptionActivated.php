<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionActivated extends Notification
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Abonnement validé',
            'message' => "L'abonnement pour ".($this->subscription->client?->name ?? $this->subscription->client_name)." a été validé par l'administrateur. Vous pouvez générer les identifiants du client.",
            'subscription_id' => $this->subscription->id,
            'url' => route('agent.subscriptions.index'),
            'icon' => 'check-circle',
            'color' => 'green',
        ];
    }
}
