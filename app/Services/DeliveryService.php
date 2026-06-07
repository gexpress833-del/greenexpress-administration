<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DeliveryService
{
    public function __construct(
        protected ActivityLogService $activityLogService,
    ) {}

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
