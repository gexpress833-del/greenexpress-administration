<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ClientCredentialsGenerated extends Notification
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
            'title' => 'Vos identifiants sont prêts',
            'message' => 'Votre compte Green Express a été créé. Vous pouvez maintenant vous connecter pour suivre votre abonnement.',
            'subscription_id' => $this->subscription->id,
            'url' => route('login'),
            'icon' => 'user-check',
            'color' => 'blue',
        ];
    }
}
