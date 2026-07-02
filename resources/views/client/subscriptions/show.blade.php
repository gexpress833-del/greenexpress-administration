<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Abonnement #{{ $subscription->id }}</h1>
        <x-back-button :href="route('client.subscriptions.index')" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Détails</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Type</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $subscription->type_label }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Date début</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $subscription->start_date?->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Date fin</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $subscription->end_date?->format('d/m/Y') }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Jours restants</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ $subscription->remaining_days }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Statut</span><span class="font-medium text-gray-800 dark:text-gray-100">{{ ucfirst($subscription->status) }}</span></div>
            </div>

            @if($subscription->status === 'active' || $subscription->status === 'expired')
                <div class="mt-6 space-y-3">
                    <form method="POST" action="{{ route('client.subscriptions.renew', $subscription) }}">
                        @csrf
                        <select name="type" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 mb-2">
                            <option value="weekly">Renouveler Hebdomadaire</option>
                            <option value="monthly">Renouveler Mensuel</option>
                        </select>
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            Renouveler
                        </button>
                    </form>

                    @if($subscription->status === 'active')
                        <form method="POST" action="{{ route('client.subscriptions.suspend', $subscription) }}">
                            @csrf
                            <input type="text" name="reason" required placeholder="Motif de suspension" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 mb-2">
                            <input type="number" name="duration_days" required min="1" placeholder="Durée (jours)" class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 mb-2">
                            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                                Demander suspension
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            @if($subscription->status === 'suspended')
                <form method="POST" action="{{ route('client.subscriptions.reactivate', $subscription) }}" class="mt-6">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                        Réactiver
                    </button>
                </form>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Menu de la semaine</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="border border-green-200 dark:border-green-800 rounded-lg p-4 bg-green-50 dark:bg-green-900/20">
                    <div class="text-xs font-bold text-green-700 dark:text-green-300 uppercase mb-1">Aujourd'hui</div>
                    <div class="text-sm text-gray-800 dark:text-gray-100">
                        @if($todayOrder && $todayOrder->items->first())
                            {{ $todayOrder->items->first()->meal->name }}
                        @else
                            Aucun repas prévu
                        @endif
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        @if($todayOrder)
                            Livraison : {{ ucfirst($todayOrder->delivery?->status ?? 'en attente') }}
                        @endif
                    </div>
                </div>
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Demain</div>
                    <div class="text-sm text-gray-800 dark:text-gray-100">
                        @if($tomorrowOrder && $tomorrowOrder->items->first())
                            {{ $tomorrowOrder->items->first()->meal->name }}
                        @else
                            Aucun repas prévu
                        @endif
                    </div>
                </div>
            </div>

            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mt-6 mb-4">Historique des repas</h2>
            @forelse($history as $order)
                <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-3 mb-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">{{ $order->delivery_date?->format('d/m/Y') }}</span>
                        <span class="text-gray-800 dark:text-gray-100">
                            @if($order->items->first())
                                {{ $order->items->first()->meal->name }}
                            @else
                                Non défini
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-gray-500 dark:text-gray-400">Statut</span>
                        <span class="text-gray-800 dark:text-gray-100">{{ ucfirst($order->delivery?->status ?? 'en attente') }}</span>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucun repas dans l'historique</p>
            @endforelse
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Suspensions</h2>
            @forelse($subscription->suspensions as $sus)
                <div class="border border-gray-100 dark:border-gray-700 rounded-lg p-3 mb-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Motif</span><span class="text-gray-800 dark:text-gray-100">{{ $sus->reason }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Durée</span><span class="text-gray-800 dark:text-gray-100">{{ $sus->duration_days }} jours</span></div>
                    <div class="flex justify-between"><span class="text-gray-500 dark:text-gray-400">Statut</span><span class="text-gray-800 dark:text-gray-100">{{ ucfirst($sus->status) }}</span></div>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">Aucune suspension</p>
            @endforelse
        </div>
    </div>
</x-app-layout>

