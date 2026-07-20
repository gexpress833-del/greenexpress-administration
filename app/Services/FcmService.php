<?php

namespace App\Services;

use App\Models\FcmToken;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class FcmService
{
    public const NOTIFICATION_TAG = 'green-express';

    public const NOTIFICATION_ICON = '/logo-192.png';

    public const NOTIFICATION_BADGE = '/logo-192.png';

    public function registerToken(User $user, string $token, ?string $platform = null, ?string $deviceId = null): FcmToken
    {
        $tokenHash = hash('sha256', $token);

        FcmToken::updateOrCreate(
            ['token_hash' => $tokenHash],
            [
                'user_id' => $user->id,
                'token' => $token,
                'platform' => $platform,
                'device_id' => $deviceId,
                'last_used_at' => now(),
                'revoked_at' => null,
            ],
        );

        return FcmToken::where('token_hash', $tokenHash)->firstOrFail();
    }

    public function revokeToken(User $user, string $token): void
    {
        $user->fcmTokens()->where('token_hash', hash('sha256', $token))->update(['revoked_at' => now()]);
    }

    public function sendNotification(Notification $notification): void
    {
        $this->sendToUser(
            $notification->user,
            $notification->title,
            $notification->message,
            $notification->url ?? route('notifications.history'),
            [
                'notification_id' => (string) $notification->id,
                'type' => (string) ($notification->type ?? 'custom'),
                'category' => (string) ($notification->category ?? 'information'),
            ],
        );
    }

    public function sendToUser(User $user, string $title, string $body, string $url = '/notifications', array $extraData = []): void
    {
        $tokens = $user->fcmTokens()->whereNull('revoked_at')->get();
        if ($tokens->isEmpty()) {
            return;
        }

        $credentials = $this->credentials();
        $projectId = config('services.firebase.project_id') ?: ($credentials['project_id'] ?? null);
        if (! $projectId) {
            return;
        }

        $accessToken = $this->accessToken($credentials);
        $endpoint = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        foreach ($tokens as $token) {
            $data = array_merge([
                'title' => $title,
                'body' => $body,
                'url' => $url,
                'icon' => self::NOTIFICATION_ICON,
                'badge' => self::NOTIFICATION_BADGE,
                'sound' => 'default',
                'tag' => self::NOTIFICATION_TAG,
            ], $extraData);

            try {
                Http::withToken($accessToken)->post($endpoint, [
                    'message' => [
                        'token' => $token->token,
                        'data' => $data,
                        'android' => [
                            'notification' => [
                                'icon' => self::NOTIFICATION_ICON,
                                'color' => '#16a34a',
                                'tag' => self::NOTIFICATION_TAG,
                                'click_action' => $url,
                            ],
                        ],
                        'apns' => [
                            'payload' => [
                                'aps' => [
                                    'sound' => 'default',
                                    'badge' => 1,
                                ],
                            ],
                        ],
                        'fcm_options' => [
                            'analytics_label' => $extraData['type'] ?? 'custom',
                        ],
                    ],
                ])->throw();

                $token->update(['last_used_at' => now()]);
            } catch (RequestException $exception) {
                if (in_array($exception->response?->status(), [400, 404], true)) {
                    $token->update(['revoked_at' => now()]);
                }

                Log::warning('FCM notification delivery failed.', [
                    'token_id' => $token->id,
                    'status' => $exception->response?->status(),
                ]);
            }
        }
    }

    private function accessToken(array $credentials): string
    {
        $now = time();
        $header = $this->base64Url(json_encode(['alg' => 'RS256', 'typ' => 'JWT'], JSON_THROW_ON_ERROR));
        $claim = $this->base64Url(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ], JSON_THROW_ON_ERROR));
        $unsignedToken = $header.'.'.$claim;

        if (! openssl_sign($unsignedToken, $signature, $credentials['private_key'], OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Firebase service account JWT signing failed.');
        }

        return Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $unsignedToken.'.'.$this->base64Url($signature),
        ])->throw()->json('access_token');
    }

    private function credentials(): array
    {
        $path = config('services.firebase.service_account_json');
        if (! is_string($path) || ! is_file($path)) {
            throw new RuntimeException('Firebase service account file is missing.');
        }

        return json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    private function base64Url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
