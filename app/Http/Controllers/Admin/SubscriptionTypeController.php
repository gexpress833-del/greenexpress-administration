<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionType;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionTypeController extends Controller
{
    public function index()
    {
        $types = SubscriptionType::orderBy('display_order')->get();
        return view('admin.subscription_types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.subscription_types.create');
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
            'is_active' => 'boolean',
            // display_order is computed automatically
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        // assign automatic display order (append to end)
        $maxOrder = SubscriptionType::max('display_order');
        $validated['display_order'] = is_null($maxOrder) ? 0 : ($maxOrder + 1);

        // Normalize prices according to selected currency
        $currencyService = app(\App\Services\CurrencyService::class);
        $entered = (float) $validated['price'];
        if (($validated['currency'] ?? 'usd') === 'fc') {
            $validated['price_fc'] = $entered;
            $validated['price'] = $currencyService->fcToUsd($entered);
        } else {
            $validated['price'] = $entered;
            $validated['price_fc'] = $validated['price_fc'] ?? $currencyService->usdToFc($entered);
        }

        $type = SubscriptionType::create($validated);

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
        return view('admin.subscription_types.edit', compact('subscriptionType'));
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
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['display_order'] = $validated['display_order'] ?? $subscriptionType->display_order;

        $currencyService = app(\App\Services\CurrencyService::class);
        $entered = (float) $validated['price'];
        if (($validated['currency'] ?? 'usd') === 'fc') {
            $validated['price_fc'] = $entered;
            $validated['price'] = $currencyService->fcToUsd($entered);
        } else {
            $validated['price'] = $entered;
            $validated['price_fc'] = $validated['price_fc'] ?? $currencyService->usdToFc($entered);
        }

        $subscriptionType->update($validated);

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
}
