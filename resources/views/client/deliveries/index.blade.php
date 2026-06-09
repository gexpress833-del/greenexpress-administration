<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mes livraisons</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Code</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Commande</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($deliveries as $delivery)
                        <tr>
                            <td class="px-6 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $delivery->delivery_code }}</td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-300">{{ $delivery->order->code }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $delivery->status_color_class }}">
                                    {{ $delivery->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-gray-600 dark:text-gray-300">{{ $delivery->created_at?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">
                                <a href="{{ route('client.deliveries.show', $delivery) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="5">Aucune livraison</td>
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
