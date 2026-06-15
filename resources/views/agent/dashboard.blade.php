<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Dashboard Agent</h1>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ now()->format('d/m/Y H:i') }}</span>
    </div>

    @if($pendingWithdrawals > 0)
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-800 dark:text-amber-200 rounded-lg flex items-center justify-between">
            <span class="font-medium">{{ $pendingWithdrawals }} demande(s) de retrait en cours de traitement</span>
            <a href="{{ route('agent.withdrawals.index') }}" class="text-sm font-semibold underline">Voir</a>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Commandes aujourd'hui</p>
            <p class="text-2xl font-bold text-green-700">{{ $todayOrders }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total commandes validées</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $totalOrders }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Points total</p>
            <p class="text-2xl font-bold text-blue-700">{{ $totalPoints }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">+{{ $todayPoints }} aujourd'hui</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Solde disponible</p>
            <p class="text-2xl font-bold text-purple-700">$ {{ number_format($availableBalance, 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Min retrait : $ 10.00</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Plafond commission</p>
            <p class="text-2xl font-bold text-amber-600">$ {{ number_format($dailyCapRemaining, 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">restant sur $ {{ number_format($dailyCap, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Bonus repas aujourd'hui</p>
            <p class="text-2xl font-bold text-pink-600">{{ $todayRewards }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">max 1/jour</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Commandes des 7 derniers jours</h2>
            @php $maxWeekly = max(!empty($weeklyOrders) ? max($weeklyOrders) : 0, 1); @endphp
            <div class="flex items-end gap-3 h-40">
                @foreach($weeklyOrders as $day => $count)
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="w-full bg-blue-100 rounded-t" style="height: {{ $maxWeekly > 0 ? ($count / $maxWeekly) * 100 : 0 }}%;">
                            <div class="w-full h-full bg-blue-500 rounded-t opacity-80"></div>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $day }}</span>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Commissions journalières (7 derniers jours)</h2>
            <div class="space-y-3">
                @forelse($commissionsByDate as $commission)
                    <div class="flex items-center justify-between py-2 border-b border-gray-50 dark:border-gray-700">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $commission->calculated_for_date?->format('d/m/Y') ?? $commission->created_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $commission->description }}</p>
                        </div>
                        <p class="text-sm font-bold text-green-700">$ {{ number_format($commission->amount_usd, 2) }}</p>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">Aucune commission pour le moment.</p>
                @endforelse
            </div>
        </div>
    </div>

    @if($badges->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-100 dark:border-gray-700 mb-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Badges récents</h2>
        <div class="flex flex-wrap gap-3">
            @foreach($badges as $badge)
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200">
                    {{ $badge->type->label() }} — {{ $badge->earned_date->format('d/m/Y') }}
                </span>
            @endforeach
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Commandes récentes</h2>
                <a href="{{ route('agent.orders.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">+ Nouvelle commande</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50"><tr><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Code</th><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Client</th><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Total</th><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Statut</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ $order->code }}</td>
                                <td class="px-4 py-2">{{ $order->client_name }}</td>
                                <td class="px-4 py-2">$ {{ number_format($order->total_amount, 2) }}<br><span class="text-xs text-gray-400 dark:text-gray-500">{{ number_format($order->total_amount_fc, 0, ',', '.') }} FC</span></td>
                                <td class="px-4 py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $order->status_color_class }}">{{ $order->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-2 text-gray-500 dark:text-gray-400" colspan="4">Aucune commande</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Top Clients</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50"><tr><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Client</th><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Commandes</th><th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Total</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topClients as $client)
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ $client->client_name }}</td>
                                <td class="px-4 py-2">{{ $client->orders_count }}</td>
                                <td class="px-4 py-2">$ {{ number_format($client->total_spent, 2) }}<br><span class="text-xs text-gray-400 dark:text-gray-500">{{ number_format($client->total_spent_fc, 0, ',', '.') }} FC</span></td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-2 text-gray-500 dark:text-gray-400" colspan="3">Aucun client</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
