<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PushSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string', 'max:500'],
            'keys.p256dh' => ['required', 'string', 'max:500'],
            'keys.auth' => ['required', 'string', 'max:500'],
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'user_id' => $request->user()?->id,
                'p256dh' => $data['keys']['p256dh'],
                'auth' => $data['keys']['auth'],
                'user_agent' => $request->userAgent(),
                'last_used_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        PushSubscription::where('endpoint', $data['endpoint'])->delete();

        return response()->json(['success' => true]);
    }

    public function publicKey(): JsonResponse
    {
        $key = config('services.webpush.public_key');

        if (!$key) {
            return response()->json(['error' => 'VAPID public key not configured'], 503);
        }

        return response()->json(['publicKey' => $key]);
    }
}
