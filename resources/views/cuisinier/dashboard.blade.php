<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Cuisine</h1>
        <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">{{ now()->format('d/m/Y H:i') }}</span>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Aujourd'hui</p>
            <p class="text-xl sm:text-2xl font-bold text-green-700">{{ $todayOrders }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">À préparer</p>
            <p class="text-xl sm:text-2xl font-bold text-amber-600">{{ $pendingOrders }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">En préparation</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-700">{{ $preparingOrders }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 sm:p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Prêtes</p>
            <p class="text-xl sm:text-2xl font-bold text-purple-700">{{ $readyOrders }}</p>
        </div>
    </div>

    {{-- Commandes récentes --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-100">Commandes en cours</h2>
            <a href="{{ route('cuisinier.orders.index') }}" class="text-xs sm:text-sm text-green-600 hover:underline font-medium">Tout voir</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Code</th>
                        <th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400 hidden sm:table-cell">Client</th>
                        <th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Repas</th>
                        <th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-medium">{{ $order->code }}</td>
                            <td class="px-4 py-3 hidden sm:table-cell">{{ $order->client_name ?? '-' }}</td>
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
                            <td class="px-4 py-3">
                                <a href="{{ route('cuisinier.orders.show', $order) }}" class="text-green-600 hover:text-green-800 text-xs sm:text-sm font-medium">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center" colspan="5">Aucune commande en cours</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
