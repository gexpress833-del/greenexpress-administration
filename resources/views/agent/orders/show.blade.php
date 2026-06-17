<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-base sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Commande <span class="break-all">{{ $order->code }}</span></h1>
        <div class="space-x-2">
            <x-back-button :href="route('agent.orders.index')" />
            <a href="{{ route('agent.receipt.show', $order) }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">Reçu</a>
            <a href="{{ route('agent.receipt.pdf', $order) }}" class="bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium py-2 px-4 rounded-lg transition">PDF</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Informations</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Client</span><span class="font-medium">{{ $order->client_name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Téléphone</span><span class="font-medium">{{ $order->client_phone }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Adresse</span><span class="font-medium">{{ $order->delivery_address }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Date livraison</span><span class="font-medium">{{ $order->delivery_date?->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Statut</span><span class="font-medium">{{ $order->status }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Total</span><span class="font-bold text-green-700">
                    @if($order->currency === 'fc')
                        {{ number_format($order->total_amount_fc, 0, ',', '.') }} FC
                    @else
                        $ {{ number_format($order->total_amount, 2) }}
                    @endif
                </span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Code validation client</span><span class="font-mono font-bold text-orange-600 dark:text-orange-400 tracking-wider">{{ $order->client_validation_code }}</span></div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Repas commandés</h2>
            <div class="space-y-2">
                @foreach($order->items as $item)
                    <div class="flex justify-between items-center py-2 border-b border-gray-50 dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-gray-100">{{ $item->meal->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Qté : {{ $item->quantity }} x
                                @if($order->currency === 'fc')
                                    {{ number_format($item->unit_price_fc, 0, ',', '.') }} FC
                                @else
                                    $ {{ number_format($item->unit_price, 2) }}
                                @endif
                            </p>
                        </div>
                        <p class="font-semibold text-gray-800 dark:text-gray-100">
                            @if($order->currency === 'fc')
                                {{ number_format($item->total_price_fc, 0, ',', '.') }} FC
                            @else
                                $ {{ number_format($item->total_price, 2) }}
                            @endif
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>

