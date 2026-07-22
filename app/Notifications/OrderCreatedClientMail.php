<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCreatedClientMail extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $items = $this->order->items->map(function ($item) {
            return $item->quantity.'x '.$item->meal->name.' — '.number_format((float) $item->total_price, 2).' USD';
        })->implode("\n");

        $total = number_format((float) $this->order->total_amount, 2);

        return (new MailMessage)
            ->subject('Confirmation de votre commande '.$this->order->code)
            ->greeting('Bonjour '.$this->order->client_name.',')
            ->line('Votre commande a été enregistrée avec succès.')
            ->line('**Numéro de commande :** '.$this->order->code)
            ->line('**Date de livraison :** '.$this->order->delivery_date->format('d/m/Y'))
            ->line('**Adresse de livraison :** '.$this->order->delivery_address)
            ->line('**Détails de la commande :**')
            ->line($items)
            ->line('**Total :** '.$total.' USD')
            ->line('Votre commande est en attente de validation par notre équipe.')
            ->action('Suivre ma commande', url('/login'))
            ->line('Merci pour votre confiance.')
            ->salutation('L\'équipe Green Express');
    }
}
