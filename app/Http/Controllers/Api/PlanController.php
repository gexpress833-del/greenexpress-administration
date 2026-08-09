<?php

namespace App\Http\Controllers\Api;

use App\Models\SubscriptionType;
use Illuminate\Http\JsonResponse;

class PlanController
{
    public function index(): JsonResponse
    {
        $plans = SubscriptionType::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'description' => $plan->description,
                    'type' => $plan->type,
                    'meals_per_day' => $plan->meals_per_day,
                    'price' => (float) $plan->price,
                    'price_fc' => (float) $plan->price_fc,
                    'duration_days' => $plan->duration_days,
                    'total_deliveries' => $plan->total_deliveries,
                ];
            });

        return response()->json($plans);
    }
}
