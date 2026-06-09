<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionRenewed extends Notification
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
                'title' => 'Réabonnement effectué',
                'message' => "{$clientName} a renouvelé son abonnement {$this->subscription->type} jusqu'au {$this->subscription->end_date->format('d/m/Y')}.",
                'subscription_id' => $this->subscription->id,
                'url' => route('admin.subscriptions.show', $this->subscription),
                'icon' => 'refresh',
                'color' => 'green',
            ];
        }

        if ($notifiable->isAgent()) {
            return [
                'title' => 'Réabonnement effectué',
                'message' => "{$clientName} a renouvelé son abonnement {$this->subscription->type} jusqu'au {$this->subscription->end_date->format('d/m/Y')}.",
                'subscription_id' => $this->subscription->id,
                'url' => route('agent.subscriptions.index'),
                'icon' => 'refresh',
                'color' => 'green',
            ];
        }

        return [
            'title' => 'Abonnement renouvelé',
            'message' => "Votre abonnement a été renouvelé jusqu'au {$this->subscription->end_date->format('d/m/Y')}.",
            'subscription_id' => $this->subscription->id,
            'url' => route('client.subscriptions.index'),
            'icon' => 'refresh',
            'color' => 'green',
        ];
    }
}
