<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\Order;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CuisinierController extends Controller
{
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        $stats = [
            'total_meals' => Meal::count(),
            'active_meals' => Meal::where('is_active', true)->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    public function menus(): JsonResponse
    {
        $menus = Meal::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function ($meal) {
                return [
                    'id' => $meal->id,
                    'name' => $meal->name,
                    'description' => $meal->description,
                    'category' => $meal->category?->name,
                    'price' => (float) $meal->price,
                    'price_fc' => (float) $meal->price_fc,
                    'image' => $meal->image && str_starts_with($meal->image, 'http') ? $meal->image : ($meal->image ? asset('storage/'.$meal->image) : null),
                    'is_available' => $meal->is_active,
                    'status' => $meal->status,
                ];
            });

        return response()->json(['data' => $menus]);
    }

    public function storeMenu(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'in:usd,fc'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['required', 'in:available,unavailable'],
            'image' => ['nullable', 'url', 'max:500'],
            'is_active' => ['boolean'],
        ]);

        $meal = Meal::create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'message' => 'Repas créé avec succès',
            'data' => [
                'id' => $meal->id,
                'name' => $meal->name,
                'price' => (float) $meal->price,
                'price_fc' => (float) $meal->price_fc,
                'image' => $meal->image,
            ],
        ], 201);
    }

    public function updateMenu(Request $request, int $id): JsonResponse
    {
        $meal = Meal::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'in:usd,fc'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['sometimes', 'in:available,unavailable'],
            'image' => ['nullable', 'url', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $meal->update($validated);

        return response()->json([
            'message' => 'Repas mis à jour avec succès',
            'data' => [
                'id' => $meal->id,
                'name' => $meal->name,
                'price' => (float) $meal->price,
                'price_fc' => (float) $meal->price_fc,
                'image' => $meal->image,
            ],
        ]);
    }

    public function destroyMenu(int $id): JsonResponse
    {
        $meal = Meal::findOrFail($id);
        $meal->delete();

        return response()->json([
            'message' => 'Repas supprimé avec succès',
        ]);
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
                    'items' => $order->items->map(function ($item) {
                        $image = $item->meal?->image;

                        return [
                            'id' => $item->id,
                            'meal_name' => $item->meal->name,
                            'quantity' => $item->quantity,
                            'image' => $image && str_starts_with($image, 'http') ? $image : ($image ? asset('storage/'.$image) : null),
                        ];
                    }),
                ];
            });

        return response()->json(['data' => $orders]);
    }
}
