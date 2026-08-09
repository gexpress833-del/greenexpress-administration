<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController
{
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        $orders = Order::with(['items.meal', 'agent', 'delivery.livreur'])
            ->where('client_id', $user->id)
            ->latest()
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
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'meal_name' => $item->meal->name,
                            'quantity' => $item->quantity,
                            'unit_price' => (float) $item->unit_price,
                            'unit_price_fc' => (float) $item->unit_price_fc,
                            'total_price' => (float) $item->total_price,
                            'total_price_fc' => (float) $item->total_price_fc,
                        ];
                    }),
                ];
            });

        return response()->json(['data' => $orders]);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load(['items.meal', 'agent', 'delivery.livreur', 'client']);

        if ($order->client_id !== Auth::id()) {
            return response()->json(['error' => 'Non autorisé'], 403);
        }

        return response()->json([
            'data' => [
                'id' => $order->id,
                'code' => $order->code,
                'status' => $order->status,
                'total_amount' => (float) $order->total_amount,
                'total_amount_fc' => (float) $order->total_amount_fc,
                'delivery_address' => $order->delivery_address,
                'delivery_date' => $order->delivery_date?->format('Y-m-d H:i'),
                'client_validation_code' => $order->client_validation_code,
                'created_at' => $order->created_at->format('Y-m-d H:i'),
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'meal_name' => $item->meal->name,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'unit_price_fc' => (float) $item->unit_price_fc,
                        'total_price' => (float) $item->total_price,
                        'total_price_fc' => (float) $item->total_price_fc,
                    ];
                }),
            ],
        ]);
    }
}
