<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mes commandes</h1>
        <a href="{{ route('agent.orders.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
            + Nouvelle commande
        </a>
    </div>

    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par code, client ou téléphone..."
               class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium py-2 px-4 rounded-lg transition">Rechercher</button>
        @if(request('search'))
            <a href="{{ route('agent.orders.index') }}" class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 text-sm font-medium py-2 px-4 rounded-lg transition">Réinitialiser</a>
        @endif
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Code</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Client</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Téléphone</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Date livraison</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-3 font-medium">{{ $order->code }}</td>
                            <td class="px-6 py-3">{{ $order->client_name }}</td>
                            <td class="px-6 py-3">{{ $order->client_phone }}</td>
                            <td class="px-6 py-3 font-medium">
                                @if($order->currency === 'fc')
                                    {{ number_format($order->total_amount_fc, 0, ',', '.') }} FC
                                @else
                                    $ {{ number_format($order->total_amount, 2) }}
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_color_class }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3">{{ $order->delivery_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 space-x-2">
                                <a href="{{ route('agent.orders.show', $order) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">Voir</a>
                                <a href="{{ route('agent.receipt.show', $order) }}" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">Reçu</a>
                                <a href="{{ route('agent.receipt.pdf', $order) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300">PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="7">Aucune commande</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
