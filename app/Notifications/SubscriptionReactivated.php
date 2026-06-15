<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionReactivated extends Notification
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $clientName = $this->subscription->client?->name ?? 'Client';

        if ($notifiable->isAdmin()) {
            return [
                'title' => 'Abonnement réactivé',
                'message' => "{$clientName} a réactivé son abonnement.",
                'subscription_id' => $this->subscription->id,
                'url' => route('admin.subscriptions.show', $this->subscription),
                'icon' => 'play-circle',
                'color' => 'green',
            ];
        }

        if ($notifiable->isAgent()) {
            return [
                'title' => 'Abonnement réactivé',
                'message' => "{$clientName} a réactivé son abonnement.",
                'subscription_id' => $this->subscription->id,
                'url' => route('agent.subscriptions.index'),
                'icon' => 'play-circle',
                'color' => 'green',
            ];
        }

        return [
            'title' => 'Abonnement réactivé',
            'message' => 'Votre abonnement est de nouveau actif.',
            'subscription_id' => $this->subscription->id,
            'url' => route('client.subscriptions.index'),
            'icon' => 'play-circle',
            'color' => 'green',
        ];
    }
}
