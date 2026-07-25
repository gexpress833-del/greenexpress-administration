<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\SubscriptionType;
use App\Services\AiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function index()
    {
        return view('help');
    }

    public function ask(Request $request, AiService $ai): JsonResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:1000'],
        ]);

        $user = $request->user();

        $context = [
            'exchange_rate' => ExchangeRate::current(),
            'subscription_types' => SubscriptionType::active()->orderBy('display_order')->get([
                'name',
                'description',
                'price',
                'price_fc',
                'duration_days',
                'meals_per_day',
            ])->toArray(),
            'user_role' => $user?->role ?? 'visiteur',
        ];

        try {
            $response = $ai->generateHelpResponse(
                $data['question'],
                $context,
                $user?->name,
                $user?->role
            );

            return response()->json(['response' => $response]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
