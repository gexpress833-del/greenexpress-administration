<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\SubscriptionType;
use App\Services\CurrencyService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionTypeController extends Controller
{
    public function index()
    {
        $types = SubscriptionType::orderBy('display_order')->get();

        return view('admin.subscription_types.index', compact('types'));
    }

    public function create()
    {
        $meals = Meal::where('status', 'available')->orderBy('name')->get();

        return view('admin.subscription_types.create', compact('meals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:usd,fc',
            'price_fc' => 'nullable|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'meals_per_day' => 'required|integer|min:1|max:3',
            'is_active' => 'boolean',
            'monday_meal_id' => 'nullable|exists:meals,id',
            'tuesday_meal_id' => 'nullable|exists:meals,id',
            'wednesday_meal_id' => 'nullable|exists:meals,id',
            'thursday_meal_id' => 'nullable|exists:meals,id',
            'friday_meal_id' => 'nullable|exists:meals,id',
            // display_order is computed automatically
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        // assign automatic display order (append to end)
        $maxOrder = SubscriptionType::max('display_order');
        $validated['display_order'] = is_null($maxOrder) ? 0 : ($maxOrder + 1);

        // Normalize prices according to selected currency
        $currencyService = app(CurrencyService::class);
        $entered = (float) $validated['price'];
        if (($validated['currency'] ?? 'usd') === 'fc') {
            $validated['price_fc'] = $entered;
            $validated['price'] = $currencyService->fcToUsd($entered);
        } else {
            $validated['price'] = $entered;
            $validated['price_fc'] = $validated['price_fc'] ?? $currencyService->usdToFc($entered);
        }

        $type = SubscriptionType::create($validated);

        $this->syncWeeklyMenus($type, $request->input('weekly_menu', []));

        // Notify all users about new subscription type
        NotificationService::notifyAllUsers(
            'Nouveau type d\'abonnement',
            "Le type d'abonnement '{$type->name}' est maintenant disponible au prix de \${$type->price}",
            'subscription_type',
            SubscriptionType::class,
            $type->id
        );

        return redirect()->route('admin.subscription-types.index')
            ->with('success', 'Type d\'abonnement créé avec succès.');
    }

    public function edit(SubscriptionType $subscriptionType)
    {
        $meals = Meal::where('status', 'available')->orderBy('name')->get();

        return view('admin.subscription_types.edit', compact('subscriptionType', 'meals'));
    }

    public function update(Request $request, SubscriptionType $subscriptionType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|in:usd,fc',
            'price_fc' => 'nullable|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'meals_per_day' => 'required|integer|min:1|max:3',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
            'monday_meal_id' => 'nullable|exists:meals,id',
            'tuesday_meal_id' => 'nullable|exists:meals,id',
            'wednesday_meal_id' => 'nullable|exists:meals,id',
            'thursday_meal_id' => 'nullable|exists:meals,id',
            'friday_meal_id' => 'nullable|exists:meals,id',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['display_order'] = $validated['display_order'] ?? $subscriptionType->display_order;

        $currencyService = app(CurrencyService::class);
        $entered = (float) $validated['price'];
        if (($validated['currency'] ?? 'usd') === 'fc') {
            $validated['price_fc'] = $entered;
            $validated['price'] = $currencyService->fcToUsd($entered);
        } else {
            $validated['price'] = $entered;
            $validated['price_fc'] = $validated['price_fc'] ?? $currencyService->usdToFc($entered);
        }

        $subscriptionType->update($validated);

        $this->syncWeeklyMenus($subscriptionType, $request->input('weekly_menu', []));

        // Notify all users about updated subscription type
        NotificationService::notifyAllUsers(
            'Type d\'abonnement modifié',
            "Le type d'abonnement '{$subscriptionType->name}' a été mis à jour. Nouveau prix: \${$subscriptionType->price}",
            'subscription_type',
            SubscriptionType::class,
            $subscriptionType->id
        );

        return redirect()->route('admin.subscription-types.index')
            ->with('success', 'Type d\'abonnement mis à jour avec succès.');
    }

    public function destroy(SubscriptionType $subscriptionType)
    {
        $subscriptionType->delete();

        return redirect()->route('admin.subscription-types.index')
            ->with('success', 'Type d\'abonnement supprimé avec succès.');
    }

    private function syncWeeklyMenus(SubscriptionType $subscriptionType, array $weeklyMenuData): void
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($weeklyMenuData as $weekNumber => $daysData) {
            $weekNumber = (int) $weekNumber;
            if ($weekNumber < 1 || $weekNumber > 4) {
                continue;
            }

            foreach ($days as $day) {
                $mealId = $daysData[$day] ?? null;
                $mealId = $mealId ? (int) $mealId : null;

                $subscriptionType->weeklyMenus()->updateOrCreate(
                    ['week_number' => $weekNumber, 'day' => $day],
                    ['meal_id' => $mealId]
                );
            }
        }

        // Ensure legacy single-week fields stay in sync with week 1 for backward compatibility
        $weekOne = $subscriptionType->weeklyMenus->where('week_number', 1)->keyBy('day');
        $subscriptionType->update([
            'monday_meal_id' => $weekOne['monday']->meal_id ?? null,
            'tuesday_meal_id' => $weekOne['tuesday']->meal_id ?? null,
            'wednesday_meal_id' => $weekOne['wednesday']->meal_id ?? null,
            'thursday_meal_id' => $weekOne['thursday']->meal_id ?? null,
            'friday_meal_id' => $weekOne['friday']->meal_id ?? null,
        ]);
    }
}
