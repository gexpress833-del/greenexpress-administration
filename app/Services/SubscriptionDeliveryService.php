<?php

namespace App\Services;

use App\Helpers\DateHelper;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionDeliveriesAvailable;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SubscriptionDeliveryService
{
    public function rewardAgent(Subscription $subscription): void {}

    public function generateDailyOrders(Subscription $subscription): void
    {
        $subscriptionType = $subscription->subscriptionType;
        if (! $subscriptionType) {
            return;
        }

        if (! $subscription->start_date || ! $subscription->end_date) {
            return;
        }

        $start = Carbon::parse($subscription->start_date);
        $end = Carbon::parse($subscription->end_date);
        $mealsPerDay = max(1, $subscriptionType->meals_per_day ?? 1);
        $deliveryDays = DateHelper::subscriptionDeliveryDays($subscription->total_days);

        $date = $start->copy();
        $count = 0;
        $todayCount = 0;
        $createdDeliveries = [];
        $today = Carbon::today();
        $existingDates = $subscription->orders()
            ->whereNotNull('delivery_date')
            ->pluck('delivery_date')
            ->map(fn ($d) => Carbon::parse($d)->startOfDay()->toDateString())
            ->all();

        while ($count < $deliveryDays) {
            if ($date->gt($end)) {
                break;
            }

            if (DateHelper::isBusinessDay($date)) {
                $skipDate = in_array($date->copy()->startOfDay()->toDateString(), $existingDates);
                if (! $skipDate) {
                    $meal = $subscriptionType->mealForDate($date);

                    for ($mealIndex = 0; $mealIndex < $mealsPerDay; $mealIndex++) {
                        $order = Order::create([
                            'code' => 'GX-SUB-'.strtoupper(Str::random(8)).'-'.$date->format('Ymd').'-'.($mealIndex + 1),
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

                        $delivery = Delivery::create([
                            'order_id' => $order->id,
                            'livreur_id' => null,
                            'delivery_code' => 'DLV-'.strtoupper(uniqid()),
                            'status' => 'pending',
                            'notes' => "Abonnement {$subscription->id} - {$date->format('d/m/Y')} - Repas ".($mealIndex + 1)."/{$mealsPerDay}",
                        ]);

                        $createdDeliveries[] = $delivery;
                        if ($date->isSameDay($today)) {
                            $todayCount++;
                        }
                    }

                    $count++;
                }
            }

            $date->addDay();

            if ($date->gt($end)) {
                break;
            }
        }

        if (count($createdDeliveries) > 0) {
            $this->notifyLivreurs($subscription, $todayCount, count($createdDeliveries));
        }
    }

    private function notifyLivreurs(Subscription $subscription, int $todayCount, int $totalCount): void
    {
        $livreurs = User::where('role', 'livreur')->get();
        if ($livreurs->isEmpty()) {
            return;
        }

        $notification = new SubscriptionDeliveriesAvailable($todayCount, $totalCount, $subscription->client_name);
        foreach ($livreurs as $livreur) {
            $livreur->notify($notification);
        }
    }
}
