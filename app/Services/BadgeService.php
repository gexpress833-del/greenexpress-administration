<?php

namespace App\Services;

use App\Enums\BadgeType;
use App\Models\Badge;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;

class BadgeService
{
    public function assignTopSellerDay(User $agent, Carbon $date): ?Badge
    {
        if (! $agent->isAgent()) {
            return null;
        }

        $already = Badge::where('agent_id', $agent->id)
            ->where('type', BadgeType::TOP_SELLER_DAY->value)
            ->where('earned_date', $date)
            ->first();

        if ($already) {
            return $already;
        }

        $count = Order::where('agent_id', $agent->id)
            ->where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereDate('client_validated_at', $date)
            ->count();

        if ($count < 5) {
            return null;
        }

        return Badge::create([
            'agent_id' => $agent->id,
            'type' => BadgeType::TOP_SELLER_DAY->value,
            'earned_date' => $date,
            'description' => "Top vendeur du jour avec {$count} commandes validées",
        ]);
    }

    public function assignActiveAgent(User $agent, Carbon $date): ?Badge
    {
        if (! $agent->isAgent()) {
            return null;
        }

        $already = Badge::where('agent_id', $agent->id)
            ->where('type', BadgeType::ACTIVE_AGENT->value)
            ->where('earned_date', $date)
            ->first();

        if ($already) {
            return $already;
        }

        $count = Order::where('agent_id', $agent->id)
            ->where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereDate('client_validated_at', $date)
            ->count();

        if ($count < 3) {
            return null;
        }

        return Badge::create([
            'agent_id' => $agent->id,
            'type' => BadgeType::ACTIVE_AGENT->value,
            'earned_date' => $date,
            'description' => "Agent actif avec {$count} commandes validées",
        ]);
    }

    public function assignDeliveryChampion(User $agent, Carbon $date): ?Badge
    {
        if (! $agent->isAgent()) {
            return null;
        }

        $already = Badge::where('agent_id', $agent->id)
            ->where('type', BadgeType::DELIVERY_CHAMPION->value)
            ->where('earned_date', $date)
            ->first();

        if ($already) {
            return $already;
        }

        $count = Order::where('agent_id', $agent->id)
            ->where('status', 'delivered')
            ->whereNotNull('client_validated_at')
            ->whereDate('client_validated_at', $date)
            ->count();

        if ($count < 10) {
            return null;
        }

        return Badge::create([
            'agent_id' => $agent->id,
            'type' => BadgeType::DELIVERY_CHAMPION->value,
            'earned_date' => $date,
            'description' => "Champion livraison avec {$count} commandes validées",
        ]);
    }

    public function assignBadgesForDate(User $agent, Carbon $date): array
    {
        $badges = [];
        $badges[] = $this->assignTopSellerDay($agent, $date);
        $badges[] = $this->assignActiveAgent($agent, $date);
        $badges[] = $this->assignDeliveryChampion($agent, $date);
        return array_filter($badges);
    }
}
