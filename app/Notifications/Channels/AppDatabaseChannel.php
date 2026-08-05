<?php

namespace App\Notifications\Channels;

use App\Jobs\SendFcmNotification;
use App\Models\Notification as AppNotification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AppDatabaseChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $data = $this->getData($notifiable, $notification);

        // Create notification in app_notifications table
        $appNotification = AppNotification::create([
            'user_id' => $notifiable->id,
            'title' => data_get($data, 'title', 'Notification'),
            'message' => data_get($data, 'message', data_get($data, 'body', '')),
            'type' => data_get($data, 'type', 'custom'),
            'category' => data_get($data, 'category', 'information'),
            'url' => data_get($data, 'url'),
            'whatsapp_link' => data_get($data, 'whatsapp_link'),
            'notifiable_type' => $notifiable->getMorphClass(),
            'notifiable_id' => $notifiable->id,
            'is_read' => false,
        ]);

        // Dispatch FCM notification job
        try {
            SendFcmNotification::dispatch($appNotification);
        } catch (\Throwable $exception) {
            Log::warning('FCM notification dispatch failed.', [
                'notification_id' => $appNotification->id,
                'user_id' => $notifiable->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Get the data for the notification.
     *
     * @return array<string, mixed>
     */
    protected function getData(object $notifiable, Notification $notification): array
    {
        if (method_exists($notification, 'toApp')) {
            return $notification->toApp($notifiable);
        }

        if (method_exists($notification, 'toDatabase')) {
            $data = $notification->toDatabase($notifiable);

            // Handle DatabaseMessage objects
            if ($data instanceof DatabaseMessage) {
                return $data->data;
            }

            return $data;
        }

        return $notification->toArray($notifiable);
    }
}
