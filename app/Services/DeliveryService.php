<?php

namespace App\Services;

use App\Models\Delivery;
use Illuminate\Support\Facades\DB;

class DeliveryService
{
    public function __construct(
        protected ActivityLogService $activityLogService,
    ) {}

    public function canDeliver(Delivery $delivery): array
    {
        $order = $delivery->order;

        if ($order->client_validated_at !== null) {
            return ['allowed' => false, 'message' => 'Cette commande a déjà été validée par le client.'];
        }

        $subscription = $order->subscription;

        if (! $subscription) {
            return ['allowed' => true, 'message' => ''];
        }

        if (! $subscription->isActive()) {
            return ['allowed' => false, 'message' => 'L\'abonnement n\'est pas actif.'];
        }

        $today = today();
        $startDate = $subscription->start_date?->startOfDay();
        $endDate = $subscription->end_date?->startOfDay();

        if ($startDate && $endDate && ($today->lt($startDate) || $today->gt($endDate))) {
            return ['allowed' => false, 'message' => 'La date actuelle n\'est pas comprise dans la période d\'abonnement.'];
        }

        $consumedDays = $subscription->consumedDays();
        if ($subscription->total_days && $consumedDays >= $subscription->total_days) {
            return ['allowed' => false, 'message' => 'Le quota de jours d\'abonnement est atteint.'];
        }

        $alreadyDeliveredToday = $subscription->orders()
            ->where('delivery_date', $today)
            ->where('status', 'delivered')
            ->whereKeyNot($order->id)
            ->count();

        $mealsPerDay = max(1, $subscription->subscriptionType?->meals_per_day ?? 1);
        if ($alreadyDeliveredToday >= $mealsPerDay) {
            return ['allowed' => false, 'message' => 'Le nombre de repas autorisés aujourd\'hui pour cet abonnement est déjà atteint.'];
        }

        return ['allowed' => true, 'message' => ''];
    }

    public function assign(Delivery $delivery, int $livreurId): void
    {
        DB::transaction(function () use ($delivery, $livreurId) {
            $delivery->livreur_id = $livreurId;
            $delivery->status = 'assigned';
            $delivery->picked_up_at = now();
            $delivery->save();

            $delivery->order->status = 'delivering';
            $delivery->order->save();
        });
    }

    public function deliver(Delivery $delivery): void
    {
        DB::transaction(function () use ($delivery) {
            $delivery->status = 'delivered';
            $delivery->delivered_at = now();
            $delivery->save();

            $order = $delivery->order;
            $order->status = 'delivered';
            $order->delivered_at = now();
            $order->save();
        });
    }

    public function validateByClient(Delivery $delivery, string $code): array
    {
        $order = $delivery->order;

        if (strtoupper($code) !== $order->client_validation_code) {
            return ['success' => false, 'message' => 'Code de validation incorrect.'];
        }

        if ($order->client_validated_at) {
            return ['success' => true, 'message' => 'Cette livraison a déjà été validée.'];
        }

        DB::transaction(function () use ($order, $delivery) {
            $order->client_validated_at = now();
            $order->delivered_at = now();
            $order->status = 'delivered';
            $order->save();

            $delivery->status = 'delivered';
            $delivery->delivered_at = now();
            $delivery->save();
        });

        return ['success' => true, 'message' => 'Livraison validée.'];
    }
}
