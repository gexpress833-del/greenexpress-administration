<?php

namespace App\Listeners;

use App\Events\OrderValidatedByClient;
use App\Services\LivreurPointService;

class CreditLivreurOnDeliveryValidation
{
    public function __construct(
        protected LivreurPointService $livreurPointService,
    ) {}

    public function handle(OrderValidatedByClient $event): void
    {
        $order = $event->order;
        $delivery = $order->delivery;

        if (! $delivery || ! $delivery->livreur_id) {
            return;
        }

        // Crédite 8 points au livreur pour la livraison validée.
        $this->livreurPointService->creditForDelivery($delivery);
    }
}
