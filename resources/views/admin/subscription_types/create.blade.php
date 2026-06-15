<x-app-layout>
    <div class="mb-6 flex items-center gap-3">
        <x-back-button :href="route('admin.subscription-types.index')" />
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nouveau type d'abonnement</h1>
    </div>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.subscription-types.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom</label>
                <input type="text" name="name" required value="{{ old('name') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea name="description" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix (USD)</label>
                    <input type="number" name="price" step="0.01" min="0" required value="{{ old('price') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                    @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix (FC)</label>
                    <input type="number" name="price_fc" step="1" min="0" required value="{{ old('price_fc') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                    @error('price_fc')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Durée (jours)</label>
                    <input type="number" name="duration_days" min="1" required value="{{ old('duration_days', 7) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Devise</label>
                    <select name="currency" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="usd" {{ strtolower(old('currency', 'USD')) === 'usd' ? 'selected' : '' }}>USD ($)</option>
                        <option value="fc" {{ strtolower(old('currency', 'USD')) === 'fc' ? 'selected' : '' }}>Francs congolais (FC)</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    {{-- display_order is assigned automatically --}}
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">Ordre d'affichage : automatique 
                        <span class="ml-1 text-xs text-gray-400" title="L'ordre est généré automatiquement à la création">&#9432;</span>
                    </p>
                </div>
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Actif</label>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.subscription-types.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-sm">Annuler</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">Créer</button>
            </div>
        </form>
    </div>
</x-app-layout>
