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

        $deliveryInfo = $this->order->delivery_date->format('d/m/Y');
        if ($this->order->delivery_time) {
            $deliveryInfo .= ' à '.$this->order->delivery_time;
        }

        return (new MailMessage)
            ->subject('Nouvelle commande '.$this->order->code.' créée')
            ->greeting('Bonjour,')
            ->line('Une nouvelle commande a été créée par '.$this->order->agent->name.'.')
            ->line('**Numéro de commande :** '.$this->order->code)
            ->line('**Client :** '.$this->order->client_name.' — '.$this->order->client_phone)
            ->line('**Livraison :** '.$deliveryInfo)
            ->line('**Adresse :** '.$this->order->delivery_address)
            ->line('**Détails :**')
            ->line($items)
            ->line('**Total :** '.$total.' USD')
            ->line('La commande est en attente de validation par l\'administrateur.')
            ->action('Voir la commande', url('/admin/orders/'.$this->order->id))
            ->salutation('L\'équipe Green Express');
    }
}
