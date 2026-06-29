<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionPending extends Notification
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
            'title' => 'Nouvel abonnement en attente',
            'message' => "Un nouvel abonnement pour {$this->subscription->client_name} a été créé par {$this->subscription->agent->name} et est en attente de validation.",
            'subscription_id' => $this->subscription->id,
            'url' => route('admin.subscriptions.show', ['subscription' => $this->subscription->id]),
            'icon' => 'clipboard-list',
            'color' => 'amber',
        ];
    }
}
