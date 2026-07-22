<?php

namespace App\Console\Commands;

use App\Services\RecoveryBonusService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:award-recovery-bonus')]
#[Description('Award 4 recovery bonus points every 5 hours to active agents and livreurs until Sunday 23:59')]
class AwardHourlyCompensationPoints extends Command
{
    public function handle(RecoveryBonusService $bonusService): int
    {
        if (! $bonusService->isActive()) {
            $this->info('La période de compensation est terminée. Retour au fonctionnement normal.');

            return self::SUCCESS;
        }

        $inserted = $bonusService->awardBonus();

        if ($inserted > 0) {
            $this->info("{$inserted} compensation(s) de 4 points attribuée(s) pour {$bonusService->currentPeriodKey()}.");
        } else {
            $this->info('Aucune nouvelle compensation à attribuer pour cette période.');
        }

        $notified = $bonusService->sendBonusNotification();
        if ($notified > 0) {
            $this->info("{$notified} notification(s) de bonus envoyée(s).");
        }

        return self::SUCCESS;
    }
}
