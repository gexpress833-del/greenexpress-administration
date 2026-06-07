<?php

namespace App\Listeners;

use App\Events\OrderValidatedByClient;
use App\Services\BadgeService;
use App\Services\PointService;
use App\Services\RewardService;

class CreditAgentOnOrderValidation
{
    public function __construct(
        protected PointService $pointService,
        protected RewardService $rewardService,
        protected BadgeService $badgeService,
    ) {}

    public function handle(OrderValidatedByClient $event): void
    {
        $order = $event->order;
        $agent = $order->agent;

        if (! $agent || ! $agent->isAgent()) {
            return;
        }

        // Points immédiats
        $this->pointService->creditForOrder($order);

        // Vérifier bonus repas
        $this->rewardService->checkDailyMealBonus($agent);

        // Badges journaliers
        $this->badgeService->assignBadgesForDate($agent, today());
    }
}
