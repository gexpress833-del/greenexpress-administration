<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Livraison {{ $delivery->delivery_code }}</h1>
        <x-back-button :href="route('livreur.deliveries.index')" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Informations</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Code livraison</span><span class="font-medium">{{ $delivery->delivery_code }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Commande</span><span class="font-medium">{{ $delivery->order->code }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Client</span><span class="font-medium">{{ $delivery->order->client_name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Téléphone</span><span class="font-medium">{{ $delivery->order->client_phone }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Adresse</span><span class="font-medium">{{ $delivery->order->delivery_address }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Statut</span><span class="font-medium">{{ $delivery->status }}</span></div>
                @if($delivery->status === 'delivered')
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Code validation</span><span class="font-mono font-bold text-orange-600 dark:text-orange-400 tracking-wider">{{ $delivery->order->client_validation_code }}</span></div>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Repas à livrer</h2>
            <div class="space-y-2">
                @foreach($delivery->order->items as $item)
                    <div class="flex justify-between items-center py-2 border-b border-gray-50 dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-gray-100">{{ $item->meal->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Qté : {{ $item->quantity }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($delivery->status === 'delivered')
                <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 rounded-lg text-center font-medium">
                    Livraison effectuée le {{ $delivery->delivered_at?->format('d/m/Y H:i') }}
                </div>
            @elseif($delivery->livreur_id === null)
                <form action="{{ route('livreur.deliveries.assign', $delivery) }}" method="POST" class="mt-6" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    <button type="submit" :disabled="loading" class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-500 text-white font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2 disabled:cursor-not-allowed">
                        <template x-if="loading">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                        </template>
                        <span x-text="loading ? 'Chargement...' : 'Prendre en charge cette livraison'">Prendre en charge cette livraison</span>
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('livreur.deliveries.notify', $delivery) }}" class="mt-6" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    <button type="submit" :disabled="loading" class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-blue-500 text-white font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2 disabled:cursor-not-allowed">
                        <template x-if="loading">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                        </template>
                        <span x-text="loading ? 'Envoi...' : 'Avertir le client (WhatsApp)'">Avertir le client (WhatsApp)</span>
                    </button>
                </form>

                <form method="POST" action="{{ route('livreur.deliveries.validate-by-code', $delivery) }}" class="mt-3" x-data="{ loading: false }" @submit="loading = true">
                    @csrf
                    <div class="mb-2">
                        <label for="validation_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code de validation client</label>
                        <input type="text" name="validation_code" id="validation_code" maxlength="6" placeholder="Ex: A3B9K7"
                               value="{{ session('validation_code') }}"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed"
                               required :readonly="loading">
                    </div>
                    <button type="submit" :disabled="loading" class="w-full bg-amber-600 hover:bg-amber-700 disabled:bg-amber-500 text-white font-semibold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2 disabled:cursor-not-allowed">
                        <template x-if="loading">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                            </svg>
                        </template>
                        <span x-text="loading ? 'Validation...' : 'Valider la livraison par code'">Valider la livraison par code</span>
                    </button>
                </form>

            @endif
        </div>
    </div>
</x-app-layout>
