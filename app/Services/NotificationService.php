<?php

namespace App\Services;

class NotificationService
{
    /**
     * Notify all users about an event. Minimal no-op implementation for tests.
     *
     * @param string $title
     * @param string $message
     * @param string|null $type
     * @param string|null $relatedClass
     * @param int|null $relatedId
     * @return void
     */
    public static function notifyAllUsers(string $title, string $message, ?string $type = null, ?string $relatedClass = null, ?int $relatedId = null): void
    {
        // Intentionally left blank for test environment. Replace with real implementation as needed.
    }
}
