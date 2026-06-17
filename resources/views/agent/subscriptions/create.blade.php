<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nouvel abonnement client</h1>
        <x-back-button :href="route('agent.subscriptions.index')" />
    </div>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('agent.subscriptions.store') }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom client</label>
                <input type="text" name="client_name" required value="{{ old('client_name') }}" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone</label>
                <input type="text" name="client_phone" required value="{{ old('client_phone') }}" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="client_email" required value="{{ old('client_email') }}" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type d'abonnement</label>
                    <select name="subscription_type_id" required :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                        @forelse($subscriptionTypes as $type)
                            <option value="{{ $type->id }}" {{ old('subscription_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ $type->duration_days }} jours) - ${{ number_format($type->price, 2) }}
                            </option>
                        @empty
                            <option disabled>Aucun type disponible</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date début</label>
                    <input type="date" name="start_date" required value="{{ old('start_date', now()->format('Y-m-d')) }}" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Devise</label>
                    <select name="currency" required :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                        <option value="usd" {{ old('currency') === 'fc' ? '' : 'selected' }}>USD ($)</option>
                        <option value="fc" {{ old('currency') === 'fc' ? 'selected' : '' }}>Francs congolais (FC)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix</label>
                    <input type="number" step="0.01" name="price" required value="{{ old('price') }}" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                </div>
            </div>

            <button type="submit" :disabled="loading" class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-500 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2 disabled:cursor-not-allowed">
                <template x-if="loading">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                </template>
                <span x-text="loading ? 'Enregistrement...' : 'Enregistrer l\'abonnement'">Enregistrer l'abonnement</span>
            </button>
        </form>
    </div>
</x-app-layout>
