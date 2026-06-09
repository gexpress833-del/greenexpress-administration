<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mon Espace</h1>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('d/m/Y H:i') }}</span>
    </div>

    @if($activeSubscription && $activeSubscription->remaining_days <= 3 && $activeSubscription->status === 'active')
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200 rounded-lg flex items-center justify-between">
            <span class="font-medium">Votre abonnement expire dans {{ $activeSubscription->remaining_days }} jour(s). Pensez à le renouveler.</span>
            <a href="{{ route('client.subscriptions.index') }}" class="text-sm font-semibold underline">Voir</a>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Abonnement</p>
            <p class="text-2xl font-bold text-green-700 dark:text-green-400">
                {{ $activeSubscription ? ucfirst($activeSubscription->status) : 'Aucun' }}
            </p>
            @if($activeSubscription)
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $activeSubscription->remaining_days }} jours restants</p>
            @endif
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total dépensé</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">$ {{ number_format($totalSpent, 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($totalSpentFc, 0, ',', '.') }} FC</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Commandes</p>
            <p class="text-2xl font-bold text-blue-700 dark:text-blue-400">{{ $totalOrders }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $pendingOrders }} en cours / {{ $deliveredOrders }} livrées</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Livraisons à venir</p>
            <p class="text-2xl font-bold text-purple-700 dark:text-purple-400">{{ $upcomingDeliveries->count() }}</p>
        </div>
    </div>

    @if($upcomingDeliveries->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Prochaines livraisons</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50"><tr><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Code</th><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Date</th><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Adresse</th><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Statut</th></tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($upcomingDeliveries as $order)
                            <tr>
                                <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100">{{ $order->code }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $order->delivery_date?->format('d/m/Y') }}</td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $order->delivery_address }}</td>
                                <td class="px-4 py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $order->status_color_class }}">{{ $order->status }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Commandes récentes</h2>
            <a href="{{ route('client.subscriptions.index') }}" class="text-sm text-green-600 dark:text-green-400 hover:underline">Mes abonnements</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50"><tr><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Code</th><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Date livraison</th><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Total</th><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Statut</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentOrders as $order)
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100">{{ $order->code }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">{{ $order->delivery_date?->format('d/m/Y') }}</td>
                            <td class="px-4 py-2 text-gray-600 dark:text-gray-300">$ {{ number_format($order->total_amount, 2) }}<br><span class="text-xs text-gray-400 dark:text-gray-500">{{ number_format($order->total_amount_fc, 0, ',', '.') }} FC</span></td>
                            <td class="px-4 py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $order->status_color_class }}">{{ $order->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-2 text-gray-500 dark:text-gray-400" colspan="4">Aucune commande</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
