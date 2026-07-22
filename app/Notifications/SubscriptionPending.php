<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionPending extends Notification
{
    use Queueable;

    public function __construct(public Subscription $subscription) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvel abonnement en attente de validation')
            ->greeting('Bonjour,')
            ->line("Un nouvel abonnement pour {$this->subscription->client_name} a été créé par {$this->subscription->agent->name}.")
            ->line('**Type :** '.$this->subscription->type)
            ->line('**Client :** '.$this->subscription->client_name.' — '.$this->subscription->client_phone)
            ->line('**Période :** '.$this->subscription->start_date->format('d/m/Y').' au '.$this->subscription->end_date->format('d/m/Y'))
            ->line('**Prix :** '.number_format((float) $this->subscription->price, 2).' USD')
            ->line('Cet abonnement est en attente de validation par l\'administrateur.')
            ->action('Voir l\'abonnement', url('/admin/subscriptions/'.$this->subscription->id))
            ->salutation('L\'équipe Green Express');
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
