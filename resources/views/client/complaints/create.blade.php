<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Nouvelle réclamation</h1>
        <x-back-button :href="route('client.orders.show', $order)" />
    </div>

    <div class="max-w-lg mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Commande : <span class="font-medium text-gray-800 dark:text-gray-200">{{ $order->code }}</span></p>
        <form method="POST" action="{{ route('client.complaints.store', $order) }}">
            @csrf
            <div class="mb-4">
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type de réclamation</label>
                <select name="type" id="type" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" required>
                    <option value="">Choisir...</option>
                    <option value="missing_item">Article manquant</option>
                    <option value="wrong_item">Mauvais article</option>
                    <option value="late_delivery">Livraison en retard</option>
                    <option value="quality_issue">Problème de qualité</option>
                    <option value="other">Autre</option>
                </select>
                @error('type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea name="description" id="description" rows="4" maxlength="1000" class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500" required></textarea>
                @error('description')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-3 px-4 rounded-lg transition">Envoyer la réclamation</button>
        </form>
    </div>
</x-app-layout>

