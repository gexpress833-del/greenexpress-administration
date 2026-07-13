<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Réclamation #{{ $complaint->id }}</h1>
        <x-back-button :href="route('admin.complaints.index')" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Détails</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Commande</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $complaint->order->code ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Client</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $complaint->client->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Téléphone</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $complaint->client->phone ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Type</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $complaint->type_label }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Statut</span><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $complaint->status_color_class }}">{{ $complaint->status }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Date</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $complaint->created_at?->format('d/m/Y H:i') }}</span></div>
                @if($complaint->resolved_at)
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Traité par</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $complaint->resolver->name ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Date traitement</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $complaint->resolved_at?->format('d/m/Y H:i') }}</span></div>
                @endif
            </div>

            <div class="mt-6">
                <h3 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Description</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">{{ $complaint->description }}</p>
            </div>

            @if($complaint->admin_response)
                <div class="mt-4">
                    <h3 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Réponse admin</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 bg-green-50 dark:bg-green-900/20 p-3 rounded-lg border border-green-100 dark:border-green-800">{{ $complaint->admin_response }}</p>
                </div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Traiter la réclamation</h2>
            <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Statut</label>
                    <select name="status" id="status" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option value="open" {{ $complaint->status === 'open' ? 'selected' : '' }}>Ouvert</option>
                        <option value="in_progress" {{ $complaint->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                        <option value="resolved" {{ $complaint->status === 'resolved' ? 'selected' : '' }}>Résolu</option>
                        <option value="rejected" {{ $complaint->status === 'rejected' ? 'selected' : '' }}>Rejeté</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="admin_response" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Réponse (optionnel)</label>
                    <textarea name="admin_response" id="admin_response" rows="4" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ $complaint->admin_response }}</textarea>
                </div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">Mettre à jour</button>
            </form>
        </div>
    </div>
</x-app-layout>

