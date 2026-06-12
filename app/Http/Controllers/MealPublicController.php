<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;

class MealPublicController extends Controller
{
    public function index(Request $request)
    {
        $meals = Meal::where('is_active', true)
            ->with('category')
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->where('category_id', $request->category);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->search . '%';
                $q->where(function ($sub) use ($term) {
                    $sub->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $categories = \App\Models\Category::whereHas('meals', fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();

        return view('meals.public', compact('meals', 'categories'));
    }
}
