<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        protected CurrencyService $currencyService,
        protected ActivityLogService $activityLogService,
    ) {}

    public function createOrder(array $data, int $agentId): Order
    {
        return DB::transaction(function () use ($data, $agentId) {
            $total = 0;
            $totalFc = 0;

            $order = Order::create([
                'code' => 'GX-' . strtoupper(uniqid()),
                'client_validation_code' => strtoupper(Str::random(6)),
                'agent_id' => $agentId,
                'client_name' => $data['client_name'],
                'client_phone' => $data['client_phone'],
                'delivery_address' => $data['delivery_address'],
                'delivery_date' => $data['delivery_date'],
                'currency' => $data['currency'],
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'total_amount' => 0,
                'total_amount_fc' => 0,
            ]);

            foreach ($data['items'] as $item) {
                $meal = Meal::findOrFail($item['meal_id']);
                $unitPrice = $meal->price;
                $unitPriceFc = $meal->price_fc ?: $this->currencyService->usdToFc((float) $unitPrice);
                $quantity = $item['quantity'];
                $totalPrice = $unitPrice * $quantity;
                $totalPriceFc = $unitPriceFc * $quantity;
                $total += $totalPrice;
                $totalFc += $totalPriceFc;

                OrderItem::create([
                    'order_id' => $order->id,
                    'meal_id' => $meal->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'unit_price_fc' => $unitPriceFc,
                    'total_price' => $totalPrice,
                    'total_price_fc' => $totalPriceFc,
                ]);
            }

            $order->total_amount = $total;
            $order->total_amount_fc = $totalFc;
            $order->save();

            // Créer automatiquement la livraison (non assignée)
            Delivery::create([
                'order_id' => $order->id,
                'livreur_id' => null,
                'delivery_code' => 'DLV-' . strtoupper(uniqid()),
                'status' => 'pending',
            ]);

            return $order;
        });
    }

    public function getAgentValidatedOrdersQuery(int $agentId)
    {
        return Order::where('agent_id', $agentId)
            ->where('status', 'delivered')
            ->whereNotNull('client_validated_at');
    }
}
