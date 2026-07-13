<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Meal;
use App\Services\CloudinaryService;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

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

    public function store(Request $request, CloudinaryService $cloudinary)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'in:usd,fc'],
            'price_fc' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['required', 'in:available,unavailable'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $cloudinary->upload($request->file('image'), 'meals');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $currencyService = app(CurrencyService::class);
        $entered = (float) $data['price'];
        if (($data['currency'] ?? 'usd') === 'fc') {
            // entered price is in FC
            $data['price_fc'] = $entered;
            $data['price'] = $currencyService->fcToUsd($entered);
        } else {
            // entered price is in USD
            $data['price'] = $entered;
            $data['price_fc'] = $data['price_fc'] ?? $currencyService->usdToFc($entered);
        }

        Meal::create($data);

        return redirect()->route('admin.meals.index')->with('success', 'Repas créé.');
    }

    public function edit(Meal $meal)
    {
        $categories = Category::where('is_active', true)->get();

        return view('admin.meals.edit', compact('meal', 'categories'));
    }

    public function update(Request $request, Meal $meal, CloudinaryService $cloudinary)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'in:usd,fc'],
            'price_fc' => ['nullable', 'numeric', 'min:0'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'status' => ['required', 'in:available,unavailable'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($meal->image) {
                $cloudinary->delete($meal->image);
            }
            $data['image'] = $cloudinary->upload($request->file('image'), 'meals');
        }

        $data['is_active'] = $request->boolean('is_active', true);

        $currencyService = app(CurrencyService::class);
        $entered = (float) $data['price'];
        if (($data['currency'] ?? 'usd') === 'fc') {
            $data['price_fc'] = $entered;
            $data['price'] = $currencyService->fcToUsd($entered);
        } else {
            $data['price'] = $entered;
            $data['price_fc'] = $data['price_fc'] ?? $currencyService->usdToFc($entered);
        }

        $meal->update($data);

        return redirect()->route('admin.meals.index')->with('success', 'Repas mis à jour.');
    }

    public function destroy(Meal $meal, CloudinaryService $cloudinary)
    {
        if ($meal->image) {
            $cloudinary->delete($meal->image);
        }
        $meal->delete();

        return redirect()->route('admin.meals.index')->with('success', 'Repas supprimé.');
    }

    public function toggleStatus(Meal $meal)
    {
        $meal->status = $meal->status === 'available' ? 'unavailable' : 'available';
        $meal->save();

        return redirect()->route('admin.meals.index')
            ->with('success', 'Statut du repas mis à jour.');
    }
}
