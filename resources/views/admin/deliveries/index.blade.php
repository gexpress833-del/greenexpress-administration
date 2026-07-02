<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Livraisons</h1>
        <div class="flex gap-2">
            <form method="GET" action="{{ route('admin.deliveries.index') }}" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Code livraison, commande, livreur..."
                       class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-64">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">Rechercher</button>
                @if(request('search'))
                    <a href="{{ route('admin.deliveries.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium py-2 px-4 rounded-lg transition">Réinitialiser</a>
                @endif
            </form>
            <a href="{{ route('admin.deliveries.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
                + Assigner
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Code</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Commande</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Livreur</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($deliveries as $del)
                        <tr>
                            <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $del->delivery_code }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $del->order->code }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $del->livreur?->name ?? '-' }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $del->status_color_class }}">
                                    {{ $del->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $del->created_at?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3" x-data="{ open: false }">
                                @if($del->livreur_id)
                                    <button @click="open = true" type="button" class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium">Sanctionner</button>
                                    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-sm shadow-lg" @click.outside="open = false">
                                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-2">Retirer des points</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Livreur : {{ $del->livreur?->name }}</p>
                                            <form method="POST" action="{{ route('admin.deliveries.penalize', $del) }}">
                                                @csrf
                                                <div class="mb-3">
                                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Points à retirer</label>
                                                    <input type="number" name="points" min="1" required class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                                                </div>
                                                <div class="mb-4">
                                                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Motif</label>
                                                    <input type="text" name="description" required maxlength="255" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm">
                                                </div>
                                                <div class="flex justify-end gap-2">
                                                    <button @click="open = false" type="button" class="px-3 py-1 text-sm text-gray-600 dark:text-gray-300">Annuler</button>
                                                    <button type="submit" class="px-3 py-1 text-sm bg-red-600 hover:bg-red-700 text-white rounded">Confirmer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Non assignée</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="6">Aucune livraison</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $deliveries->links() }}
        </div>
    </div>
</x-app-layout>
