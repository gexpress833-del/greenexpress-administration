<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nouvel abonnement client</h1>
        <x-back-button :href="route('agent.subscriptions.index')" />
    </div>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('agent.subscriptions.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom client</label>
                <input type="text" name="client_name" required value="{{ old('client_name') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Téléphone</label>
                <input type="text" name="client_phone" required value="{{ old('client_phone') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                <input type="email" name="client_email" required value="{{ old('client_email') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                    <select name="type" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="weekly">Hebdomadaire (7 jours)</option>
                        <option value="monthly">Mensuel (30 jours)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date début</label>
                    <input type="date" name="start_date" required value="{{ old('start_date', now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Devise</label>
                    <select name="currency" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="usd" {{ old('currency') === 'fc' ? '' : 'selected' }}>USD ($)</option>
                        <option value="fc" {{ old('currency') === 'fc' ? 'selected' : '' }}>Francs congolais (FC)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix</label>
                    <input type="number" step="0.01" name="price" required value="{{ old('price') }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                Enregistrer l'abonnement
            </button>
        </form>
    </div>
</x-app-layout>
