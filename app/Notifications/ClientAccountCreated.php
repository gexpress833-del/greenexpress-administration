<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ClientAccountCreated extends Notification
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
        public string $tempPassword
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre compte Green Express a été créé')
            ->greeting('Bonjour ' . $this->subscription->client_name . ',')
            ->line('Votre compte Green Express a été créé avec succès suite à votre abonnement.')
            ->line('Voici vos identifiants de connexion :')
            ->line('**Email :** ' . $this->subscription->client_email)
            ->line('**Mot de passe temporaire :** ' . $this->tempPassword)
            ->line('Pour votre sécurité, veuillez modifier ce mot de passe dès votre première connexion.')
            ->action('Se connecter', url('/login'))
            ->line('Merci pour votre confiance.')
            ->salutation('L\'équipe Green Express');
    }

    public function toDatabase(object $notifiable): array
    {
        // Generate WhatsApp link with credentials
        $whatsapp = app(\App\Services\WhatsAppService::class);
        $whatsappLink = $whatsapp->credentialsLink(
            $this->subscription->client_phone,
            $this->subscription->client_email,
            $this->tempPassword
        );

        return [
            'title' => 'Compte créé',
            'message' => 'Votre compte Green Express a été créé. Vous pouvez maintenant vous connecter.',
            'subscription_id' => $this->subscription->id,
            'url' => route('client.dashboard'),
            'whatsapp_link' => $whatsappLink,
            'icon' => 'user-plus',
            'color' => 'green',
        ];
    }
}
