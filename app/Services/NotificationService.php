<?php

namespace App\Services;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notify all users by creating a Notification row for each user.
     * This is a simple implementation that can be replaced by push/broadcast logic.
     *
     * @param string $title
     * @param string $message
     * @param string|null $type
     * @param string|null $relatedClass
     * @param int|null $relatedId
     * @return void
     */
    public static function notifyAllUsers(string $title, string $message, ?string $type = null, ?string $relatedClass = null, ?int $relatedId = null, ?string $url = null): void
    {
        try {
            User::chunkById(200, function ($users) use ($title, $message, $type, $relatedClass, $relatedId, $url) {
                $now = now();
                $rows = [];
                foreach ($users as $user) {
                    $rows[] = [
                        'user_id' => $user->id,
                        'title' => $title,
                        'message' => $message,
                        'type' => $type,
                        'notifiable_type' => $relatedClass,
                        'notifiable_id' => $relatedId,
                        'url' => $url,
                        'is_read' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if (!empty($rows)) {
                    Notification::insert($rows);
                }
            });
        } catch (\Throwable $e) {
            Log::error('NotificationService::notifyAllUsers failed: ' . $e->getMessage());
        }
    }
}
