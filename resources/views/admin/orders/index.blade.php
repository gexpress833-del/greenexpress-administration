<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Commandes</h1>
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Code, client, agent..."
                   class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-64">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">Rechercher</button>
            @if(request('search'))
                <a href="{{ route('admin.orders.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-medium py-2 px-4 rounded-lg transition">Réinitialiser</a>
            @endif
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Code</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Agent</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Client</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Date livraison</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $order->code }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $order->agent->name }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $order->client_name }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100 font-medium">
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
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $order->delivery_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 space-x-2">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">Voir</a>
                                <a href="{{ route('agent.receipt.show', $order) }}" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-sm">Reçu</a>
                                <a href="{{ route('agent.receipt.pdf', $order) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 text-sm">PDF</a>
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
