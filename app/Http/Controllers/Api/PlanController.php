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
                $priceFc = (float) $plan->price_fc;
                $durationDays = $plan->duration_days;
                // Calculer le prix par semaine (7 jours)
                $priceWeek = ($priceFc / $durationDays) * 7;
                // Calculer le prix par mois (30 jours)
                $priceMonth = ($priceFc / $durationDays) * 30;

                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'meals_per_day' => $plan->meals_per_day,
                    'price' => (float) $plan->price,
                    'price_fc' => $priceFc,
                    'price_week' => $priceWeek,
                    'price_month' => $priceMonth,
                    'duration_days' => $durationDays,
                    'total_deliveries' => $durationDays * $plan->meals_per_day,
                    'currency' => $plan->currency,
                    'display_order' => $plan->display_order,
                ];
            });

        return response()->json(['data' => $plans]);
    }
}
