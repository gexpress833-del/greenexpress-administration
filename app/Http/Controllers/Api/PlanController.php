<?php

namespace App\Http\Controllers\Api;

use App\Models\SubscriptionType;
use Illuminate\Http\JsonResponse;

class PlanController
{
    public function index(): JsonResponse
    {
        $plans = SubscriptionType::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'meals_per_day' => $plan->meals_per_day,
                    'price' => (float) $plan->price,
                    'price_fc' => (float) $plan->price_fc,
                    'duration_days' => $plan->duration_days,
                    'total_deliveries' => $plan->duration_days * $plan->meals_per_day, // Calculated
                    'currency' => $plan->currency,
                    'display_order' => $plan->display_order,
                ];
            });

        return response()->json(['data' => $plans]);
    }
}
