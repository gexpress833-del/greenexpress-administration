<?php

namespace App\Http\Controllers;

use App\Services\FcmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FcmTokenController extends Controller
{
    public function store(Request $request, FcmService $fcmService): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'min:20', 'max:4096'],
            'platform' => ['nullable', 'string', 'in:android,ios,web'],
            'device_id' => ['nullable', 'string', 'max:191'],
        ]);

        $fcmService->registerToken(
            $request->user(),
            $data['token'],
            $data['platform'] ?? null,
            $data['device_id'] ?? null,
        );

        return response()->json(['message' => 'Token FCM enregistré.']);
    }

    public function destroy(Request $request, FcmService $fcmService): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'min:20', 'max:4096'],
        ]);

        $fcmService->revokeToken($request->user(), $data['token']);

        return response()->json(['message' => 'Token FCM révoqué.']);
    }
}
