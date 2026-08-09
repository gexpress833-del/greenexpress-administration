<?php

namespace App\Http\Controllers\Api;

use App\Models\Subscription;
use App\Models\SubscriptionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'subscription_type_id' => 'required|exists:subscription_types,id',
            'delivery_address' => 'required|string',
            'phone' => 'required|string',
        ]);

        $subscriptionType = SubscriptionType::findOrFail($request->subscription_type_id);

        DB::beginTransaction();
        try {
            $subscription = Subscription::create([
                'user_id' => Auth::id(),
                'subscription_type_id' => $subscriptionType->id,
                'status' => 'pending',
                'start_date' => now(),
                'end_date' => now()->addDays($subscriptionType->duration_days),
                'delivery_address' => $request->delivery_address,
                'phone' => $request->phone,
                'total_deliveries' => $subscriptionType->total_deliveries,
                'remaining_deliveries' => $subscriptionType->total_deliveries,
                'price' => $subscriptionType->price,
                'price_fc' => $subscriptionType->price_fc,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Abonnement créé avec succès',
                'subscription' => [
                    'id' => $subscription->id,
                    'status' => $subscription->status,
                    'start_date' => $subscription->start_date->format('Y-m-d'),
                    'end_date' => $subscription->end_date->format('Y-m-d'),
                    'remaining_deliveries' => $subscription->remaining_deliveries,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function renew(Request $request, int $id): JsonResponse
    {
        $subscription = Subscription::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $subscriptionType = $subscription->subscriptionType;

        DB::beginTransaction();
        try {
            $subscription->update([
                'status' => 'active',
                'start_date' => now(),
                'end_date' => now()->addDays($subscriptionType->duration_days),
                'remaining_deliveries' => $subscriptionType->total_deliveries,
                'price' => $subscriptionType->price,
                'price_fc' => $subscriptionType->price_fc,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Abonnement renouvelé avec succès',
                'subscription' => [
                    'id' => $subscription->id,
                    'status' => $subscription->status,
                    'start_date' => $subscription->start_date->format('Y-m-d'),
                    'end_date' => $subscription->end_date->format('Y-m-d'),
                    'remaining_deliveries' => $subscription->remaining_deliveries,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
