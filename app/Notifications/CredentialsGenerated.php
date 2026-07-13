<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CredentialsGenerated extends Notification
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $clientName = $this->subscription->client?->name ?? $this->subscription->client_name;
        $agentName = $this->subscription->agent?->name ?? 'Agent';

        if ($notifiable->isAdmin()) {
            return [
                'title' => 'Identifiants générés',
                'message' => "{$agentName} a généré les identifiants pour {$clientName}.",
                'subscription_id' => $this->subscription->id,
                'url' => route('admin.subscriptions.show', ['subscription' => $this->subscription->id]),
                'icon' => 'user-check',
                'color' => 'blue',
            ];
        }

        return [
            'title' => 'Vos identifiants sont prêts',
            'message' => 'Votre compte Green Express a été créé. Vous pouvez maintenant vous connecter pour suivre votre abonnement.',
            'subscription_id' => $this->subscription->id,
            'url' => route('client.dashboard'),
            'icon' => 'user-check',
            'color' => 'green',
        ];
    }
}
