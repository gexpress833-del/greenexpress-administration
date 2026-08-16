<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LivreurController extends Controller
{
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        $stats = [
            'total_deliveries' => Delivery::where('livreur_id', $user->id)->count(),
            'completed_deliveries' => Delivery::where('livreur_id', $user->id)->where('status', 'delivered')->count(),
            'pending_deliveries' => Delivery::where('livreur_id', $user->id)->where('status', 'assigned')->count(),
            'total_earnings' => Delivery::where('livreur_id', $user->id)->where('status', 'delivered')->sum('commission'),
        ];

        return response()->json(['data' => $stats]);
    }

    public function assignments(): JsonResponse
    {
        $user = Auth::user();

        $deliveries = Delivery::with(['order.items.meal', 'order.client'])
            ->where('livreur_id', $user->id)
            ->where('status', 'assigned')
            ->latest()
            ->get()
            ->map(function ($delivery) {
                return [
                    'id' => $delivery->id,
                    'order_id' => $delivery->order_id,
                    'order_code' => $delivery->order->code,
                    'client_name' => $delivery->order->client_name,
                    'delivery_address' => $delivery->order->delivery_address,
                    'delivery_date' => $delivery->order->delivery_date?->format('Y-m-d H:i'),
                    'status' => $delivery->status,
                    'items' => $delivery->order->items->map(function ($item) {
                        $image = $item->meal?->image;

                        return [
                            'meal_name' => $item->meal->name,
                            'quantity' => $item->quantity,
                            'image' => $image && str_starts_with($image, 'http') ? $image : ($image ? asset('storage/'.$image) : null),
                        ];
                    }),
                ];
            });

        return response()->json(['data' => $deliveries]);
    }

    public function validateCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'code' => ['required', 'string'],
        ]);

        $order = Order::findOrFail($validated['order_id']);

        if ($order->client_validation_code !== $validated['code']) {
            return response()->json([
                'message' => 'Code de validation incorrect',
            ], 422);
        }

        $delivery = Delivery::where('order_id', $order->id)->first();

        if ($delivery) {
            $delivery->status = 'delivered';
            $delivery->delivered_at = now();
            $delivery->save();
        }

        return response()->json([
            'message' => 'Code validé avec succès',
            'data' => [
                'order_id' => $order->id,
                'status' => 'delivered',
            ],
        ]);
    }

    public function updateLocation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
        ]);

        $user = Auth::user();

        // Stocker la location dans une table de tracking ou session
        // Pour l'instant, on retourne un succès
        return response()->json([
            'message' => 'Localisation mise à jour',
            'data' => [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ],
        ]);
    }
}
