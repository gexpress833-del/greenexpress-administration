<?php

namespace App\Observers;

use App\Models\SubscriptionType;
use App\Services\NotificationService;

class SubscriptionTypeObserver
{
    public function created(SubscriptionType $type): void
    {
        NotificationService::notifyAllUsers(
            'Nouveau type d\'abonnement',
            "L'abonnement '{$type->name}' est maintenant disponible au prix de \${$type->price} ({$type->duration_days} jours)",
            'subscription_type',
            SubscriptionType::class,
            $type->id
        );
    }

    public function updated(SubscriptionType $type): void
    {
        if ($type->wasChanged('price') || $type->wasChanged('price_fc') || $type->wasChanged('name') || $type->wasChanged('duration_days')) {
            NotificationService::notifyAllUsers(
                'Type d\'abonnement modifié',
                "L'abonnement '{$type->name}' a été mis à jour. Nouveau prix: \${$type->price} pour {$type->duration_days} jours",
                'subscription_type',
                SubscriptionType::class,
                $type->id
            );
        }
    }
}
