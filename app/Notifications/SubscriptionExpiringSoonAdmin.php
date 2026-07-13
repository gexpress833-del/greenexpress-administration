<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringSoonAdmin extends Notification
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $days = $this->subscription->daysRemaining();
        $clientName = $this->subscription->client?->name ?? $this->subscription->client_name;

        return [
            'title' => 'Abonnement client bientôt terminé',
            'message' => "L'abonnement de {$clientName} expire dans {$days} jour".($days > 1 ? 's' : '').'.',
            'subscription_id' => $this->subscription->id,
            'url' => route('admin.subscriptions.expiring'),
            'icon' => 'calendar-alert',
            'color' => 'orange',
        ];
    }
}
