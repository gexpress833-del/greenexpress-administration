<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Mes commandes</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Code</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Total USD</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Total FC</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Date livraison</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-3 font-medium">{{ $order->code }}</td>
                            <td class="px-6 py-3">$ {{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-6 py-3">{{ number_format($order->total_amount_fc, 0, ',', '.') }} FC</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_color_class }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3">{{ $order->delivery_date?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">
                                <a href="{{ route('client.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 text-sm">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500" colspan="6">Aucune commande</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
