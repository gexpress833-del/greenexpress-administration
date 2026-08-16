<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;

class SecretaireController extends Controller
{
    public function stats(): JsonResponse
    {
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'pending_subscriptions' => Subscription::where('status', 'pending')->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    public function orders(): JsonResponse
    {
        $orders = Order::with(['items.meal', 'agent', 'delivery.livreur'])
            ->latest()
            ->take(50)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'code' => $order->code,
                    'status' => $order->status,
                    'total_amount' => (float) $order->total_amount,
                    'total_amount_fc' => (float) $order->total_amount_fc,
                    'delivery_date' => $order->delivery_date?->format('Y-m-d H:i'),
                    'created_at' => $order->created_at->format('Y-m-d H:i'),
                    'client_name' => $order->client_name,
                    'agent_name' => $order->agent?->name,
                    'items' => $order->items->map(function ($item) {
                        $image = $item->meal?->image;

                        return [
                            'meal_name' => $item->meal->name,
                            'quantity' => $item->quantity,
                            'image' => $image && str_starts_with($image, 'http') ? $image : ($image ? asset('storage/'.$image) : null),
                        ];
                    }),
                ];
            });

        return response()->json(['data' => $orders]);
    }

    public function flux(): JsonResponse
    {
        $deliveries = Delivery::with(['order.items.meal', 'livreur'])
            ->latest()
            ->take(30)
            ->get()
            ->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'order_id' => $delivery->order_id,
                    'order_code' => $delivery->order->code,
                    'livreur_name' => $delivery->livreur?->name,
                    'status' => $delivery->status,
                    'delivery_date' => $delivery->order->delivery_date?->format('Y-m-d H:i'),
                    'address' => $delivery->order->delivery_address,
                ];
            });

        return response()->json(['data' => $deliveries]);
    }
}
