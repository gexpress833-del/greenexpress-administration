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
            'price_fc' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'currency' => 'required|string|max:10',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['display_order'] = $validated['display_order'] ?? 0;

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
            'price_fc' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'currency' => 'required|string|max:10',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['display_order'] = $validated['display_order'] ?? $subscriptionType->display_order;

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
