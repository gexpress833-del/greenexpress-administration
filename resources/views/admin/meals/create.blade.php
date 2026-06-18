<x-app-layout>
    <div class="mb-6 flex items-center gap-3">
        <x-back-button :href="route('admin.meals.index')" />
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nouveau repas</h1>
    </div>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.meals.store') }}" enctype="multipart/form-data" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom</label>
                <input type="text" name="name" required value="{{ old('name') }}" :readonly="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea name="description" rows="2" :readonly="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Devise</label>
                    <select name="currency" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                        <option value="usd" {{ old('currency', 'usd') === 'usd' ? 'selected' : '' }}>USD ($)</option>
                        <option value="fc" {{ old('currency') === 'fc' ? 'selected' : '' }}>Francs congolais (FC)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix</label>
                    <input type="number" step="0.01" name="price" required value="{{ old('price') }}" :readonly="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix (FC)</label>
                    <input type="number" step="1" name="price_fc" value="{{ old('price_fc') }}" :readonly="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Laissez vide pour calculer automatiquement (taux 2800)</p>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catégorie</label>
                <select name="category_id" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                    <option value="">-- Choisir --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                <select name="status" required :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                    <option value="available" {{ old('status') === 'available' ? 'selected' : '' }}>Disponible</option>
                    <option value="unavailable" {{ old('status') === 'unavailable' ? 'selected' : '' }}>Indisponible</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Image</label>
                <input type="file" name="image" accept="image/*" :disabled="loading" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 disabled:opacity-60 disabled:cursor-not-allowed">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ou lien de l'image (URL)</label>
                <input type="url" name="image_url" value="{{ old('image_url') }}" placeholder="https://exemple.com/image.jpg" :readonly="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Laissez vide si vous uploadez un fichier ci-dessus.</p>
            </div>
            <div class="mb-6 flex items-center">
                <input type="checkbox" name="is_active" value="1" checked :disabled="loading" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Actif</label>
            </div>
            <button type="submit" :disabled="loading" class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-500 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2 disabled:cursor-not-allowed">
                <template x-if="loading">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                </template>
                <span x-text="loading ? 'Création...' : 'Créer'">Créer</span>
            </button>
        </form>
    </div>
</x-app-layout>
