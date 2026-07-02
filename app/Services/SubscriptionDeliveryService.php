<?php

namespace App\Services;

use App\Helpers\DateHelper;
use App\Models\AgentPoint;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SubscriptionDeliveryService
{
    public function rewardAgent(Subscription $subscription): void
    {
        if ($subscription->agentPoints()->exists()) {
            return;
        }

        $points = $subscription->total_days <= 5 ? 25 : 50;

        AgentPoint::create([
            'agent_id' => $subscription->agent_id,
            'subscription_id' => $subscription->id,
            'order_id' => null,
            'points' => $points,
            'value_usd' => 0,
            'description' => "Points gagnés pour la création d'un abonnement {$subscription->type_label}",
            'earned_at' => now(),
        ]);
    }

    public function generateDailyOrders(Subscription $subscription): void
    {
        if ($subscription->orders()->exists()) {
            return;
        }

        $subscriptionType = $subscription->subscriptionType;
        if (! $subscriptionType) {
            return;
        }

        $start = Carbon::parse($subscription->start_date);
        $end = Carbon::parse($subscription->end_date);

        $date = $start->copy();
        $count = 0;

        while ($count < $subscription->total_days) {
            if (DateHelper::isBusinessDay($date)) {
                $meal = $subscriptionType->mealForDate($date);

                $order = Order::create([
                    'code' => 'GX-SUB-' . strtoupper(Str::random(8)) . '-' . $date->format('Ymd'),
                    'agent_id' => $subscription->agent_id,
                    'client_id' => $subscription->client_id,
                    'subscription_id' => $subscription->id,
                    'client_name' => $subscription->client_name,
                    'client_phone' => $subscription->client_phone,
                    'delivery_address' => $subscription->client?->address ?? '',
                    'delivery_date' => $date->copy(),
                    'currency' => $subscription->currency,
                    'status' => 'confirmed',
                    'total_amount' => 0,
                    'total_amount_fc' => 0,
                    'admin_validated_at' => now(),
                    'confirmed_at' => now(),
                    'client_validation_code' => strtoupper(Str::random(6)),
                ]);

                if ($meal) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'meal_id' => $meal->id,
                        'quantity' => 1,
                        'unit_price' => 0,
                        'unit_price_fc' => 0,
                        'total_price' => 0,
                        'total_price_fc' => 0,
                    ]);
                }

                Delivery::create([
                    'order_id' => $order->id,
                    'livreur_id' => null,
                    'delivery_code' => 'DLV-' . strtoupper(uniqid()),
                    'status' => 'pending',
                    'notes' => "Abonnement {$subscription->id} - {$date->format('d/m/Y')}",
                ]);

                $count++;
            }

            $date->addDay();

            if ($date->gt($end)) {
                break;
            }
        }
    }
}
