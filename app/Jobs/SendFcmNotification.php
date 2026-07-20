<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFcmNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public Notification $notification,
    ) {}

    public function handle(FcmService $fcmService): void
    {
        $fcmService->sendNotification($this->notification);
    }

    public function failed(\Throwable $exception): void
    {
        Log::warning('FCM job failed permanently.', [
            'notification_id' => $this->notification->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
