<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Livraison {{ $delivery->delivery_code }}</h1>
        <x-back-button :href="route('livreur.deliveries.index')" />
    </div>

    @if(!$isUnassigned && !$isAssignedToMe)
        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 rounded-lg text-sm font-medium">
            Cette livraison est assignée à un autre livreur ({{ $delivery->livreur?->name ?? 'N/A' }}). Les informations ci-dessous sont affichées à titre de renseignement uniquement.
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Informations</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Code livraison</span><span class="font-medium">{{ $delivery->delivery_code }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Commande</span><span class="font-medium">{{ $delivery->order->code }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Date de livraison</span><span class="font-medium">{{ $delivery->order->delivery_date?->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Client</span><span class="font-medium">{{ $delivery->order->client_name }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Téléphone</span><span class="font-medium">{{ $delivery->order->client_phone }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Adresse</span><span class="font-medium">{{ $delivery->order->delivery_address }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Agent</span><span class="font-medium">{{ $delivery->order->agent?->name ?? 'N/A' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Statut</span><span class="font-medium">{{ $delivery->status }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Livreur</span><span class="font-medium">{{ $delivery->livreur?->name ?? 'Non assigné' }}</span></div>
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

            @if($isUnassigned)
                <form action="{{ route('livreur.deliveries.assign', $delivery) }}" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                        Prendre en charge cette livraison
                    </button>
                </form>
            @elseif($isAssignedToMe)
                @if($delivery->order->client_validated_at)
                    <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 rounded-lg text-center font-medium">
                        Livraison validée par le client le {{ $delivery->order->client_validated_at?->format('d/m/Y H:i') }}
                    </div>
                    <div class="mt-3 flex justify-between items-center text-sm">
                        <span class="text-gray-500 dark:text-gray-400">Code de validation</span>
                        <span class="font-mono font-bold text-green-600 dark:text-green-400 tracking-wider">{{ $delivery->order->client_validation_code }}</span>
                    </div>
                @else
                    <form method="POST" action="{{ route('livreur.deliveries.notify', $delivery) }}" class="mt-6">
                        @csrf
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            Avertir le client (WhatsApp)
                        </button>
                    </form>

                    <form method="POST" action="{{ route('livreur.deliveries.validate-by-code', $delivery) }}" class="mt-3">
                        @csrf
                        <div class="mb-2">
                            <label for="validation_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Code de validation client</label>
                            <input type="text" name="validation_code" id="validation_code" maxlength="6" placeholder="Ex: A3B9K7"
                                   value="{{ session('validation_code') }}"
                                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-sm uppercase focus:outline-none focus:ring-2 focus:ring-green-500"
                                   required>
                        </div>
                        <button type="submit" class="w-full bg-amber-600 hover:bg-amber-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                            Valider la livraison par code
                        </button>
                    </form>
                @endif
            @else
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 rounded-lg text-center text-sm">
                    Livraison assignée à {{ $delivery->livreur?->name ?? 'un autre livreur' }}.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
