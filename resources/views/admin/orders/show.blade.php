<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-base sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Commande <span class="break-all">{{ $order->code }}</span></h1>
        <x-back-button :href="route('admin.orders.index')" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Informations</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Code</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $order->code }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Agent</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $order->agent->name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Client</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $order->client_name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Téléphone</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $order->client_phone }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Adresse</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $order->delivery_address }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Date livraison</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $order->delivery_date?->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Total</span><span class="font-bold text-green-700 dark:text-green-400">
                    @if($order->currency === 'fc')
                        {{ number_format($order->total_amount_fc, 0, ',', '.') }} FC
                    @else
                        $ {{ number_format($order->total_amount, 2) }}
                    @endif
                </span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Statut</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ ucfirst($order->status) }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Code validation client</span><span class="font-mono font-bold text-orange-600 dark:text-orange-400 tracking-wider">{{ $order->client_validation_code }}</span></div>
            </div>

            <div class="mt-6 space-y-2">
                <a href="{{ route('agent.receipt.show', $order) }}" target="_blank" class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                    Reçu client
                </a>
                <a href="{{ route('agent.receipt.pdf', $order) }}" class="block w-full text-center bg-gray-700 hover:bg-gray-800 text-white font-semibold py-2 px-4 rounded-lg transition">
                    Télécharger PDF reçu
                </a>
                <a href="{{ route('admin.orders.print', $order) }}" target="_blank" class="block w-full text-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-semibold py-2 px-4 rounded-lg transition">
                    � Exporter le bon en PDF
                </a>

                @if($order->status === 'pending')
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="inline w-full" x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" :disabled="loading" class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-500 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2 disabled:cursor-not-allowed">
                            <template x-if="loading">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Validation...' : 'Valider la commande'">Valider la commande</span>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="inline w-full" x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" :disabled="loading" class="w-full bg-red-600 hover:bg-red-700 disabled:bg-red-500 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2 disabled:cursor-not-allowed">
                            <template x-if="loading">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Annulation...' : 'Rejeter / Annuler'">Rejeter / Annuler</span>
                        </button>
                    </form>
                @elseif($order->status === 'confirmed')
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="inline w-full" x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" :disabled="loading" class="w-full bg-red-600 hover:bg-red-700 disabled:bg-red-500 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2 disabled:cursor-not-allowed">
                            <template x-if="loading">
                                <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Annulation...' : 'Annuler la commande'">Annuler la commande</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Repas</h2>
            @foreach($order->items as $item)
                <div class="flex justify-between items-center py-2 border-b border-gray-50 dark:border-gray-700 text-sm">
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $item->meal->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Qté: {{ $item->quantity }} x
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
</x-app-layout>

