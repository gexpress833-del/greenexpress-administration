<?php

namespace App\Enums;

enum BadgeType: string
{
    case TOP_SELLER_DAY = 'top_seller_day';
    case ACTIVE_AGENT = 'active_agent';
    case DELIVERY_CHAMPION = 'delivery_champion';
    case BEST_WEEK = 'best_week';
    case AGENT_MONTH = 'agent_month';

    public function label(): string
    {
        return match ($this) {
            self::TOP_SELLER_DAY => 'Top vendeur du jour',
            self::ACTIVE_AGENT => 'Agent actif',
            self::DELIVERY_CHAMPION => 'Champion livraison',
            self::BEST_WEEK => 'Meilleur commercial semaine',
            self::AGENT_MONTH => 'Agent du mois Green Express',
        };
    }
}
