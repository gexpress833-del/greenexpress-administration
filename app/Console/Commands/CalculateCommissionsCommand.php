<?php

namespace App\Console\Commands;

use App\Jobs\CalculateDailyCommissions;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateCommissionsCommand extends Command
{
    protected $signature = 'commissions:calculate {--date= : Date au format Y-m-d (défaut: aujourd\'hui)}';

    protected $description = 'Calcule les commissions journalières pour tous les agents';

    public function handle(): int
    {
        $dateInput = $this->option('date');
        $date = $dateInput ? Carbon::parse($dateInput) : today();

        $this->info("Calcul des commissions pour le {$date->format('d/m/Y')}...");

        CalculateDailyCommissions::dispatch($date);

        $this->info('Job mis en file d\'attente.');

        return self::SUCCESS;
    }
}
