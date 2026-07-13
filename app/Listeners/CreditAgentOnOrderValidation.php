<?php

namespace App\Listeners;

use App\Events\OrderValidatedByClient;
use App\Services\BadgeService;
use App\Services\NotificationService;
use App\Services\PointService;
use App\Services\RewardService;

class CreditAgentOnOrderValidation
{
    public function __construct(
        protected PointService $pointService,
        protected RewardService $rewardService,
        protected BadgeService $badgeService,
        protected NotificationService $notificationService,
    ) {}

    public function handle(OrderValidatedByClient $event): void
    {
        $order = $event->order;
        $agent = $order->agent;

        if (! $agent || ! $agent->isAgent()) {
            return;
        }

        // Points immédiats (uniquement pour les commandes non-abonnement)
        $agentPoint = $this->pointService->creditForOrder($order);

        if ($agentPoint) {
            $this->notificationService->agentOrderValidated($agent, $order, $agentPoint->points);
        }

        // Vérifier bonus repas
        $reward = $this->rewardService->checkDailyMealBonus($agent);
        if ($reward) {
            $this->notificationService->agentDailyBonusEarned($agent, $reward);
        }

        // Badges journaliers
        $badges = $this->badgeService->assignBadgesForDate($agent, today());
        foreach ($badges as $badge) {
            $this->notificationService->agentBadgeEarned($agent, $badge);
        }
    }
}
