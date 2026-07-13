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
        $typeLabel = match ($this->subscription->type) {
            'weekly' => 'hebdomadaire',
            'monthly' => 'mensuel',
            default => $this->subscription->type,
        };

        if ($notifiable->isAdmin()) {
            return [
                'title' => 'Renouvellement en attente de validation',
                'message' => "{$clientName} a demandé le renouvellement de son abonnement ({$typeLabel}). En attente de validation.",
                'subscription_id' => $this->subscription->id,
                'url' => route('admin.subscriptions.show', $this->subscription),
                'icon' => 'refresh',
                'color' => 'orange',
            ];
        }

        if ($notifiable->isAgent()) {
            return [
                'title' => 'Renouvellement en attente de validation',
                'message' => "{$clientName} a demandé le renouvellement de son abonnement ({$typeLabel}). En attente de validation.",
                'subscription_id' => $this->subscription->id,
                'url' => route('agent.subscriptions.index'),
                'icon' => 'refresh',
                'color' => 'orange',
            ];
        }

        return [
            'title' => 'Demande de renouvellement envoyée',
            'message' => "Votre demande de renouvellement ({$typeLabel}) est en attente de validation par l'administrateur.",
            'subscription_id' => $this->subscription->id,
            'url' => route('client.subscriptions.index'),
            'icon' => 'refresh',
            'color' => 'orange',
        ];
    }
}
