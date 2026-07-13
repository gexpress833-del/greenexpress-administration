<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Modifier le type d'abonnement</h1>
    </div>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.subscription-types.update', $subscriptionType) }}">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom</label>
                <input type="text" name="name" required value="{{ old('name', $subscriptionType->name) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea name="description" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('description', $subscriptionType->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix (USD)</label>
                    <input type="number" name="price" step="0.01" min="0" required value="{{ old('price', $subscriptionType->price) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                    @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix (FC)</label>
                    <input type="number" name="price_fc" step="1" min="0" required value="{{ old('price_fc', $subscriptionType->price_fc) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                    @error('price_fc')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Durée (jours)</label>
                    <input type="number" name="duration_days" min="1" required value="{{ old('duration_days', $subscriptionType->duration_days) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Repas / jour</label>
                    <input type="number" name="meals_per_day" min="1" max="3" required value="{{ old('meals_per_day', $subscriptionType->meals_per_day) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Devise</label>
                    <select name="currency" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="usd" {{ strtolower(old('currency', $subscriptionType->currency)) === 'usd' ? 'selected' : '' }}>USD ($)</option>
                        <option value="fc" {{ strtolower(old('currency', $subscriptionType->currency)) === 'fc' ? 'selected' : '' }}>Francs congolais (FC)</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Menus hebdomadaires (4 semaines pour les abonnements mensuels)</p>
                <div class="space-y-4">
                    @foreach([1, 2, 3, 4] as $week)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <p class="text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase mb-2">Semaine {{ $week }}</p>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach(['monday' => 'Lundi', 'tuesday' => 'Mardi', 'wednesday' => 'Mercredi', 'thursday' => 'Jeudi', 'friday' => 'Vendredi'] as $day => $label)
                                    @php
                                        $existingMenu = $subscriptionType->weeklyMenus->where('week_number', $week)->where('day', $day)->first();
                                    @endphp
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ $label }}</label>
                                        <select name="weekly_menu[{{ $week }}][{{ $day }}]" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 text-sm">
                                            <option value="">Aucun plat</option>
                                            @foreach($meals as $meal)
                                                <option value="{{ $meal->id }}" {{ old("weekly_menu.$week.$day", $existingMenu?->meal_id) == $meal->id ? 'selected' : '' }}>{{ $meal->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">Ordre d'affichage : automatique (généré à la création)
                        <span class="ml-1 text-xs text-gray-400" title="L'ordre est généré automatiquement à la création">&#9432;</span>
                    </p>
                </div>
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subscriptionType->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Actif</label>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.subscription-types.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-sm">Annuler</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">Enregistrer</button>
            </div>
        </form>
    </div>
</x-app-layout>
