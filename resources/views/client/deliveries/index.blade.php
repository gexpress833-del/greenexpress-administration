<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Mes livraisons</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Code</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Commande</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Date</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($deliveries as $delivery)
                        <tr>
                            <td class="px-6 py-3 font-medium">{{ $delivery->delivery_code }}</td>
                            <td class="px-6 py-3">{{ $delivery->order->code }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $delivery->status_color_class }}">
                                    {{ $delivery->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3">{{ $delivery->created_at?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">
                                <a href="{{ route('client.deliveries.show', $delivery) }}" class="text-blue-600 hover:text-blue-800 text-sm">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500" colspan="5">Aucune livraison</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $deliveries->links() }}
        </div>
    </div>
</x-app-layout>
