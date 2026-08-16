<?php

namespace App\Http\Controllers\Api;

use App\Models\Meal;
use Illuminate\Http\JsonResponse;

class MenuController
{
    public function index(): JsonResponse
    {
        $menus = Meal::with('category')
            ->where('is_active', true)
            ->orderBy('category_id')
            ->orderBy('name')
            ->get()
            ->map(function ($meal) {
                return [
                    'id' => $meal->id,
                    'name' => $meal->name,
                    'description' => $meal->description,
                    'category' => $meal->category?->name,
                    'category_id' => $meal->category_id,
                    'price' => (float) $meal->price,
                    'price_fc' => (float) $meal->price_fc,
                    'image' => $meal->image && str_starts_with($meal->image, 'http') ? $meal->image : ($meal->image ? asset('storage/'.$meal->image) : null),
                    'is_available' => $meal->is_active,
                ];
            });

        return response()->json(['data' => $menus]);
    }
}
