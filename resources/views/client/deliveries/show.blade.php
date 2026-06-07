<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Livraison {{ $delivery->delivery_code }}</h1>
        <x-back-button :href="route('client.deliveries.index')" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Code livraison</span><span class="font-medium">{{ $delivery->delivery_code }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Commande</span><span class="font-medium">{{ $delivery->order->code }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Livreur</span><span class="font-medium">{{ $delivery->livreur->name ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Statut</span><span class="font-medium">{{ ucfirst($delivery->status) }}</span></div>
                @if($delivery->delivered_at)
                    <div class="flex justify-between"><span class="text-gray-500">Livrée le</span><span class="font-medium">{{ $delivery->delivered_at->format('d/m/Y H:i') }}</span></div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Repas livrés</h2>
            @foreach($delivery->order->items as $item)
                <div class="flex justify-between items-center py-2 border-b border-gray-50 text-sm">
                    <div>
                        <p class="font-medium">{{ $item->meal->name }}</p>
                        <p class="text-xs text-gray-500">Qté: {{ $item->quantity }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>

