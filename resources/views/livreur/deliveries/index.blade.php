<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mes livraisons</h1>
        <x-back-button :href="route('livreur.dashboard')" />
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par code livraison, commande ou client..."
               class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium py-2 px-4 rounded-lg transition">Rechercher</button>
        @if(request('search'))
            <a href="{{ route('livreur.deliveries.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 text-sm font-medium py-2 px-4 rounded-lg transition">Réinitialiser</a>
        @endif
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Code</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Commande</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Code validation</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Adresse</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($deliveries as $delivery)
                        <tr>
                            <td class="px-6 py-3 font-medium">{{ $delivery->delivery_code }}</td>
                            <td class="px-6 py-3">{{ $delivery->order->code }}</td>
                            <td class="px-6 py-3 font-mono text-orange-600 font-semibold tracking-wider">
                                @if($delivery->status === 'delivered')
                                    {{ $delivery->order->client_validation_code }}
                                @else
                                    <span class="text-gray-400 dark:text-gray-500 font-normal">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">{{ $delivery->order->delivery_address ?? '-' }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $delivery->status_color_class }}">
                                    {{ $delivery->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                @if($delivery->livreur_id === null)
                                    <form action="{{ route('livreur.deliveries.assign', $delivery) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-1.5 px-3 rounded-lg transition">Prendre en charge</button>
                                    </form>
                                @else
                                    <a href="{{ route('livreur.deliveries.show', $delivery) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Détails</a>
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
