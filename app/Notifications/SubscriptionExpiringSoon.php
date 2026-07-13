<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionExpiringSoon extends Notification
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

        return [
            'title' => 'Votre abonnement expire bientôt',
            'message' => "Votre abonnement se termine dans {$days} jour".($days > 1 ? 's' : '').'. Renouvelez-le pour ne pas interrompre vos repas.',
            'subscription_id' => $this->subscription->id,
            'url' => route('client.subscriptions.index'),
            'icon' => 'calendar',
            'color' => 'orange',
        ];
    }
}
