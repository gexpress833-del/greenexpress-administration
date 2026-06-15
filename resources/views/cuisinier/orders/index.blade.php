<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Commandes à préparer</h1>
        <x-back-button :href="route('cuisinier.dashboard')" />
    </div>

    <form method="GET" class="mb-4 flex flex-col sm:flex-row gap-2">
        <select name="status" class="border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-xs sm:text-sm">
            <option value="">Tous les statuts</option>
            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>À préparer</option>
            <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>En préparation</option>
            <option value="delivering" {{ request('status') === 'delivering' ? 'selected' : '' }}>Prêtes</option>
        </select>
        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-xs sm:text-sm font-medium py-2 px-4 rounded-lg transition">Filtrer</button>
        @if(request('status'))
            <a href="{{ route('cuisinier.orders.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 text-xs sm:text-sm font-medium py-2 px-4 rounded-lg transition text-center">Réinitialiser</a>
        @endif
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Code</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400 hidden md:table-cell">Client</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Repas</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-medium">{{ $order->code }}</td>
                            <td class="px-4 py-3 hidden md:table-cell">{{ $order->client_name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs">{{ $order->items->pluck('meal.name')->implode(', ') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $order->status === 'confirmed' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : '' }}
                                    {{ $order->status === 'preparing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                    {{ $order->status === 'delivering' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : '' }}">
                                    {{ $order->status === 'confirmed' ? 'À préparer' : '' }}
                                    {{ $order->status === 'preparing' ? 'En préparation' : '' }}
                                    {{ $order->status === 'delivering' ? 'Prête' : '' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs">{{ $order->created_at->format('d/m H:i') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('cuisinier.orders.show', $order) }}" class="text-green-600 hover:text-green-800 text-xs sm:text-sm font-medium">Détails</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center" colspan="6">Aucune commande</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
