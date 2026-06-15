<?php

namespace App\Jobs;

use App\Services\CommissionService;
use App\Services\LeaderboardService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateDailyCommissions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public ?Carbon $date = null)
    {
        $this->date = $date ?? today();
    }

    public function handle(
        CommissionService $commissionService,
        LeaderboardService $leaderboardService,
    ): void {
        $commissionService->calculateAllDailyCommissions($this->date);
        $leaderboardService->calculateWeekly($this->date);
    }
}
