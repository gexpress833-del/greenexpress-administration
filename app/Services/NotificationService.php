<?php

namespace App\Services;

use App\Jobs\SendFcmNotification;
use App\Models\AgentReward;
use App\Models\Badge;
use App\Models\Delivery;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create an app notification for a specific user.
     */
    public function notify(User $user, string $category, string $title, string $message, string $type = 'custom', ?string $url = null, ?string $whatsappLink = null): void
    {
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'category' => $category,
            'url' => $url,
            'whatsapp_link' => $whatsappLink,
            'is_read' => false,
        ]);

        try {
            SendFcmNotification::dispatch($notification);
        } catch (\Throwable $exception) {
            Log::warning('FCM notification dispatch failed.', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Notify all users by creating a Notification row for each user.
     */
    public static function notifyAllUsers(string $title, string $message, ?string $type = null, ?string $relatedClass = null, ?int $relatedId = null, ?string $url = null, string $category = 'information'): void
    {
        try {
            User::chunkById(200, function ($users) use ($title, $message, $type, $relatedClass, $relatedId, $url, $category) {
                foreach ($users as $user) {
                    $notification = Notification::create([
                        'user_id' => $user->id,
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                        'category' => $category,
                        'notifiable_type' => $relatedClass,
                        'notifiable_id' => $relatedId,
                        'url' => $url,
                        'is_read' => false,
                    ]);

                    try {
                        SendFcmNotification::dispatch($notification);
                    } catch (\Throwable $exception) {
                        Log::warning('FCM broadcast notification failed.', [
                            'notification_id' => $notification->id,
                            'user_id' => $user->id,
                            'error' => $exception->getMessage(),
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error('NotificationService::notifyAllUsers failed: '.$e->getMessage());
        }
    }

    /**
     * 1. Agent: Création d'un abonnement
     */
    public function agentSubscriptionCreated(User $agent, Subscription $subscription): void
    {
        $clientName = $subscription->client?->name ?? $subscription->client_name;

        $this->notify(
            $agent,
            'subscription',
            '🎉 Nouvelle demande enregistrée !',
            "Félicitations ! Vous avez enregistré un nouvel abonnement pour {$clientName}. Dès que l'administration validera cet abonnement, vos points de parrainage seront automatiquement crédités. Merci de contribuer au développement de Green Express.",
            'subscription_created',
            route('agent.subscriptions.index'),
        );
    }

    /**
     * 2. Agent: Validation de l'abonnement.
     */
    public function agentSubscriptionValidated(User $agent, Subscription $subscription): void
    {
        $clientName = $subscription->client?->name ?? $subscription->client_name;

        $this->notify(
            $agent,
            'information',
            '✅ Abonnement validé',
            "L'abonnement de {$clientName} a été validé. Les points sont crédités uniquement après validation de chaque commande livrée.",
            'subscription_validated',
            route('agent.subscriptions.index'),
        );
    }

    /**
     * 3. Agent: Renouvellement validé
     */
    public function agentSubscriptionRenewed(User $agent, Subscription $subscription): void
    {
        $clientName = $subscription->client?->name ?? $subscription->client_name;

        $this->notify(
            $agent,
            'reward',
            '🔄 Abonnement renouvelé !',
            "Bonne nouvelle ! {$clientName} a renouvelé son abonnement. Vos points de fidélisation ont été crédités. Merci de fidéliser vos clients.",
            'subscription_renewed',
            route('agent.subscriptions.index'),
        );
    }

    /**
     * 4. Livreur: Prise en charge d'une livraison (commande normale)
     */
    public function livreurDeliveryAssigned(User $livreur, Delivery $delivery): void
    {
        $order = $delivery->order;
        $clientName = $order->client_name ?? 'Client';
        $address = $order->delivery_address ?? 'Non spécifiée';

        $this->notify(
            $livreur,
            'delivery',
            '📦 Nouvelle livraison',
            "Une nouvelle commande vous a été attribuée. Client : {$clientName}. Adresse : {$address}. Merci d'effectuer la livraison dans les meilleurs délais.",
            'delivery_assigned',
            route('livreur.deliveries.show', $delivery),
        );
    }

    /**
     * 4b. Livreur: Prise en charge d'une livraison d'abonnement
     */
    public function livreurSubscriptionDeliveryAssigned(User $livreur, Delivery $delivery): void
    {
        $order = $delivery->order;
        $clientName = $order->client_name ?? 'Client';

        $this->notify(
            $livreur,
            'delivery',
            '🍽️ Livraison d\'abonnement',
            "Vous avez été affecté à la livraison du repas quotidien de {$clientName}. Une validation du client vous permettra d'obtenir vos points de livraison.",
            'delivery_assigned',
            route('livreur.deliveries.show', $delivery),
        );
    }

    /**
     * 5. Livreur: Livraison marquée comme livrée (en attente de validation)
     */
    public function livreurDeliveryPending(User $livreur, Delivery $delivery): void
    {
        $this->notify(
            $livreur,
            'information',
            '⏳ En attente de validation',
            'Votre livraison a été enregistrée. Les points seront attribués dès que le client confirmera la réception via son code ou son QR Code.',
            'delivery_pending',
            route('livreur.deliveries.show', $delivery),
        );
    }

    /**
     * 6. Livreur: Validation client (13 points)
     */
    public function livreurDeliveryValidated(User $livreur, Delivery $delivery, bool $isSubscription = false): void
    {
        $order = $delivery->order;
        $clientName = $order->client_name ?? 'Client';

        if ($isSubscription) {
            $title = '🎉 Livraison d\'abonnement validée !';
            $message = "Le repas quotidien a été confirmé par le client. 13 points viennent d'être ajoutés à votre compte.";
        } else {
            $title = '🎉 Livraison validée !';
            $message = "Félicitations ! Le client {$clientName} a confirmé la réception de sa commande. Vous recevez 13 points. Continuez ainsi pour augmenter votre score.";
        }

        $this->notify(
            $livreur,
            'reward',
            $title,
            $message,
            'delivery_validated',
            route('livreur.deliveries.show', $delivery),
        );
    }

    /**
     * 7. Agent: Commande normale validée (12 points)
     */
    public function agentOrderValidated(User $agent, Order $order, int $points): void
    {
        $clientName = $order->client_name ?? 'Client';

        if ($points >= 12) {
            $title = '⭐ Nouvelle récompense !';
            $message = "La commande de {$clientName} vient d'être validée. Vous gagnez {$points} points. Continuez à satisfaire vos clients.";
        } else {
            $title = '⭐ Points crédités';
            $message = "Votre client a confirmé sa commande. Vous recevez {$points} points.";
        }

        $this->notify(
            $agent,
            'reward',
            $title,
            $message,
            'order_validated',
            route('agent.orders.show', $order),
        );
    }

    /**
     * 8. Agent: Bonus quotidien obtenu
     */
    public function agentDailyBonusEarned(User $agent, AgentReward $reward): void
    {
        $this->notify(
            $agent,
            'bonus',
            '🎁 Bonus quotidien obtenu !',
            'Félicitations ! Vous avez débloqué votre bonus quotidien. Continuez vos performances pour obtenir davantage de récompenses.',
            'daily_bonus',
            route('agent.dashboard'),
        );
    }

    /**
     * 9. Agent: Badge obtenu
     */
    public function agentBadgeEarned(User $agent, Badge $badge): void
    {
        $badgeName = $badge->description ?? $badge->type;

        $this->notify(
            $agent,
            'badge',
            '🏅 Nouveau badge obtenu !',
            "Vous venez d'obtenir le badge : {$badgeName}. Merci pour votre régularité.",
            'badge_earned',
            route('agent.dashboard'),
        );
    }

    /**
     * Client: Abonnement validé par l'admin
     */
    public function clientSubscriptionValidated(User $client, Subscription $subscription): void
    {
        $endDate = $subscription->end_date?->format('d/m/Y') ?? '—';

        $this->notify(
            $client,
            'subscription',
            '🎉 Abonnement activé !',
            "Votre abonnement a été validé par l'administration. Il est actif jusqu'au {$endDate}. Vous pouvez maintenant consulter vos repas quotidiens et suivre vos livraisons.",
            'subscription_activated',
            route('client.subscriptions.show', $subscription),
        );
    }

    /**
     * Client: Renouvellement envoyé
     */
    public function clientSubscriptionRenewalSent(User $client, Subscription $subscription): void
    {
        $typeLabel = match ($subscription->type) {
            'weekly' => 'hebdomadaire',
            'monthly' => 'mensuel',
            default => $subscription->type,
        };

        $this->notify(
            $client,
            'subscription',
            '🔄 Renouvellement demandé',
            "Votre demande de renouvellement ({$typeLabel}) a été envoyée. Elle sera traitée par l'administration dans les meilleurs délais.",
            'subscription_renewal_sent',
            route('client.subscriptions.index'),
        );
    }

    /**
     * Client: Suspension envoyée
     */
    public function clientSubscriptionSuspended(User $client, Subscription $subscription, string $reason, int $days): void
    {
        $this->notify(
            $client,
            'subscription',
            '⏸️ Suspension demandée',
            "Votre demande de suspension de {$days} jour(s) a été enregistrée. Motif : {$reason}. Vous serez notifié dès sa validation.",
            'subscription_suspended',
            route('client.subscriptions.index'),
        );
    }

    /**
     * Client: Réactivation envoyée
     */
    public function clientSubscriptionReactivated(User $client, Subscription $subscription): void
    {
        $this->notify(
            $client,
            'subscription',
            '▶️ Réactivation demandée',
            "Votre demande de réactivation a été envoyée. Elle sera traitée par l'administration.",
            'subscription_reactivated',
            route('client.subscriptions.index'),
        );
    }

    /**
     * Client: Livreur assigné à sa commande
     */
    public function clientDeliveryAssigned(User $client, Delivery $delivery): void
    {
        $livreurName = $delivery->livreur?->name ?? 'un livreur';
        $orderCode = $delivery->order?->code ?? '—';

        $this->notify(
            $client,
            'delivery',
            '🚚 Livreur en route',
            "{$livreurName} a pris en charge votre commande {$orderCode}. Préparez-vous à recevoir votre livraison.",
            'delivery_assigned',
            route('client.orders.index'),
        );
    }

    /**
     * Client: Livraison en cours
     */
    public function clientDeliveryOnTheWay(User $client, Delivery $delivery): void
    {
        $orderCode = $delivery->order?->code ?? '—';

        $this->notify(
            $client,
            'delivery',
            '📦 Livraison en cours',
            "Votre commande {$orderCode} est en cours de livraison. Le livreur arrivera bientôt.",
            'delivery_in_progress',
            route('client.orders.index'),
        );
    }

    /**
     * Client: Commande livrée et validée
     */
    public function clientOrderDelivered(User $client, Order $order): void
    {
        $this->notify(
            $client,
            'success',
            '✅ Commande livrée !',
            "Votre commande {$order->code} a été livrée et validée. Bon appétit ! Merci de votre confiance.",
            'order_delivered',
            route('client.orders.index'),
        );
    }

    /**
     * Client: Abonnement expire bientôt
     */
    public function clientSubscriptionExpiring(User $client, Subscription $subscription, int $daysRemaining): void
    {
        $this->notify(
            $client,
            'alert',
            '⏰ Abonnement expire bientôt',
            "Votre abonnement se termine dans {$daysRemaining} jour".($daysRemaining > 1 ? 's' : '').'. Renouvelez-le dès maintenant pour ne pas interrompre vos repas.',
            'subscription_expiring',
            route('client.subscriptions.index'),
        );
    }

    /**
     * Cuisinier: Nouvelle commande à préparer
     */
    public function cuisinierNewOrder(User $cuisinier, Order $order): void
    {
        $clientName = $order->client_name ?? 'un client';
        $meals = $order->items->pluck('meal.name')->implode(', ');

        $this->notify(
            $cuisinier,
            'order',
            '🍽️ Nouvelle commande à préparer',
            "La commande {$order->code} de {$clientName} a été validée. Repas : {$meals}. Commencez la préparation dès que possible.",
            'cuisinier_new_order',
            route('cuisinier.orders.show', $order),
        );
    }

    /**
     * Admin: Cuisinier a commencé la préparation
     */
    public function cuisinierPreparationStarted(User $admin, Order $order): void
    {
        $this->notify(
            $admin,
            'order',
            '🍳 Préparation en cours',
            "La commande {$order->code} est maintenant en préparation en cuisine.",
            'cuisinier_preparation_started',
            route('admin.orders.show', $order),
        );
    }

    /**
     * Livreur + Admin: Commande prête pour livraison
     */
    public function cuisinierOrderReady(User $recipient, Order $order): void
    {
        $clientName = $order->client_name ?? 'un client';
        $address = $order->delivery_address ?? 'Non spécifiée';

        $this->notify(
            $recipient,
            'order',
            '📦 Commande prête pour livraison',
            "La commande {$order->code} de {$clientName} est prête. Adresse de livraison : {$address}.",
            'cuisinier_order_ready',
            $recipient->isLivreur()
                ? route('livreur.deliveries.index')
                : route('admin.orders.show', $order),
        );
    }
}
