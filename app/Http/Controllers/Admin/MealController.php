<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MealController extends Controller
{
    public function index()
    {
        $meals = Meal::with('category')->latest()->paginate(20);
        return view('admin.meals.index', compact('meals'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.meals.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_fc' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['required', 'in:available,unavailable'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('meals', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        if (empty($data['price_fc'])) {
            $data['price_fc'] = app(\App\Services\CurrencyService::class)->usdToFc((float) $data['price']);
        }

        Meal::create($data);

        return redirect()->route('admin.meals.index')->with('success', 'Repas créé.');
    }

    public function edit(Meal $meal)
    {
        $categories = Category::where('is_active', true)->get();
        return view('admin.meals.edit', compact('meal', 'categories'));
    }

    public function update(Request $request, Meal $meal)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_fc' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['required', 'in:available,unavailable'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($meal->image) {
                Storage::disk('public')->delete($meal->image);
            }
            $data['image'] = $request->file('image')->store('meals', 'public');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        if (empty($data['price_fc'])) {
            $data['price_fc'] = app(\App\Services\CurrencyService::class)->usdToFc((float) $data['price']);
        }

        $meal->update($data);

        return redirect()->route('admin.meals.index')->with('success', 'Repas mis à jour.');
    }

    public function destroy(Meal $meal)
    {
        if ($meal->image) {
            Storage::disk('public')->delete($meal->image);
        }
        $meal->delete();
        return redirect()->route('admin.meals.index')->with('success', 'Repas supprimé.');
    }
}
