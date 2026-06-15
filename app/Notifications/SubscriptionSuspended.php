<?php

namespace App\Notifications;

use App\Models\SubscriptionSuspension;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionSuspended extends Notification
{
    use Queueable;

    public function __construct(public SubscriptionSuspension $suspension) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $clientName = $this->suspension->subscription->client?->name ?? 'Client';
        $reason = $this->suspension->reason;
        $days = $this->suspension->duration_days;

        if ($notifiable->isAdmin()) {
            return [
                'title' => 'Demande de suspension',
                'message' => "{$clientName} demande une suspension de {$days} jour(s). Motif : {$reason}",
                'suspension_id' => $this->suspension->id,
                'url' => route('admin.suspensions.index'),
                'icon' => 'pause-circle',
                'color' => 'amber',
            ];
        }

        if ($notifiable->isAgent()) {
            return [
                'title' => 'Demande de suspension',
                'message' => "{$clientName} demande une suspension de {$days} jour(s). Motif : {$reason}",
                'suspension_id' => $this->suspension->id,
                'url' => route('agent.subscriptions.index'),
                'icon' => 'pause-circle',
                'color' => 'amber',
            ];
        }

        return [
            'title' => 'Suspension demandée',
            'message' => "Votre demande de suspension de {$days} jour(s) a été envoyée. Motif : {$reason}",
            'suspension_id' => $this->suspension->id,
            'url' => route('client.subscriptions.index'),
            'icon' => 'pause-circle',
            'color' => 'amber',
        ];
    }
}
