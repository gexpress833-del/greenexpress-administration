<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VerificateurController extends Controller
{
    public function stats(): JsonResponse
    {
        $stats = [
            'total_validations' => Order::whereNotNull('client_validation_code')->count(),
            'pending_validations' => Order::where('status', 'pending')->count(),
            'completed_validations' => Order::where('status', 'completed')->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    public function history(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);

        $orders = Order::with(['items.meal', 'agent', 'delivery.livreur'])
            ->whereNotNull('client_validation_code')
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $orders->items()->map(function ($order) {
                return [
                    'id' => $order->id,
                    'code' => $order->code,
                    'status' => $order->status,
                    'total_amount' => (float) $order->total_amount,
                    'total_amount_fc' => (float) $order->total_amount_fc,
                    'delivery_date' => $order->delivery_date?->format('Y-m-d H:i'),
                    'created_at' => $order->created_at->format('Y-m-d H:i'),
                    'client_name' => $order->client_name,
                    'client_validation_code' => $order->client_validation_code,
                    'validated_at' => $order->delivery?->delivered_at?->format('Y-m-d H:i'),
                ];
            }),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
            ],
        ]);
    }

    public function validateTicket(Request $request): JsonResponse
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

        $order->status = 'completed';
        $order->save();

        return response()->json([
            'message' => 'Ticket validé avec succès',
            'data' => [
                'order_id' => $order->id,
                'status' => 'completed',
                'validated_at' => now()->format('Y-m-d H:i'),
            ],
        ]);
    }
}
