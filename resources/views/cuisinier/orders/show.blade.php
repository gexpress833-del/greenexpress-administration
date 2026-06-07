<x-app-layout>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <h1 class="text-base sm:text-2xl font-bold text-gray-800 dark:text-gray-100">Commande <span class="break-all">{{ $order->code }}</span></h1>
        <x-back-button :href="route('cuisinier.orders.index')" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Infos commande --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-100">Détails</h2>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $order->status === 'confirmed' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : '' }}
                        {{ $order->status === 'preparing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                        {{ $order->status === 'delivering' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : '' }}">
                        {{ $order->status === 'confirmed' ? 'À préparer' : '' }}
                        {{ $order->status === 'preparing' ? 'En préparation' : '' }}
                        {{ $order->status === 'delivering' ? 'Prête pour livraison' : '' }}
                    </span>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Client</span><span class="font-medium">{{ $order->client_name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Téléphone</span><span class="font-medium">{{ $order->client_phone }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Adresse</span><span class="font-medium">{{ $order->delivery_address }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Date livraison</span><span class="font-medium">{{ $order->delivery_date?->format('d/m/Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Agent</span><span class="font-medium">{{ $order->agent->name ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Total</span><span class="font-bold text-green-700 dark:text-green-400">$ {{ number_format($order->total_amount, 2) }}</span></div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Repas à préparer</h2>
                @foreach($order->items as $item)
                    <div class="flex justify-between items-center py-3 border-b border-gray-50 dark:border-gray-700 last:border-0">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-gray-100">{{ $item->meal->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Qté: {{ $item->quantity }} x $ {{ number_format($item->unit_price, 2) }}</p>
                        </div>
                        <p class="font-semibold text-gray-800 dark:text-gray-100">$ {{ number_format($item->total_price, 2) }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-3">
                @if($order->status === 'confirmed')
                    <form method="POST" action="{{ route('cuisinier.orders.update-status', $order) }}" class="flex-1">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="preparing">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg transition">
                            Commencer la préparation
                        </button>
                    </form>
                @elseif($order->status === 'preparing')
                    <form method="POST" action="{{ route('cuisinier.orders.update-status', $order) }}" class="flex-1">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="delivering">
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 px-4 rounded-lg transition">
                            Marquer prête pour livraison
                        </button>
                    </form>
                @endif

                <a href="{{ route('cuisinier.orders.print', $order) }}" target="_blank" class="flex-1 text-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 font-semibold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Exporter en PDF
                </a>
            </div>
        </div>

        {{-- Instructions --}}
        <div class="space-y-6">
            <div class="bg-amber-50 dark:bg-amber-900/10 rounded-xl shadow-sm border border-amber-100 dark:border-amber-900/30 p-4 sm:p-6">
                <h3 class="text-base font-semibold text-amber-800 dark:text-amber-200 mb-2">Instructions</h3>
                <ul class="text-sm text-amber-700 dark:text-amber-300 space-y-2 list-disc list-inside">
                    <li>Vérifiez la quantité de chaque repas</li>
                    <li>Respectez les standards de qualité</li>
                    <li>Emballez soigneusement les repas</li>
                    <li>Joignez le reçu à la livraison</li>
                </ul>
            </div>

            @if($order->notes)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-2">Notes</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
