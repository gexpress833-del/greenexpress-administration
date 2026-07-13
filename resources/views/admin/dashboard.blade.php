<x-app-layout>
    <div class="-m-4 lg:-m-8 min-h-screen overflow-hidden bg-slate-950 text-white">
        <div class="relative min-h-screen">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(34,197,94,0.22),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,0.16),transparent_34%)]"></div>
            <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px;"></div>
            <div class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <h1 class="text-xl sm:text-2xl font-bold text-white">Tableau de bord Admin</h1>
        <span class="text-xs sm:text-sm text-slate-400">{{ now()->format('d/m/Y H:i') }}</span>
    </div>

    @if($pendingSubscriptions > 0 || $pendingWithdrawals > 0)
        <div class="mb-6 space-y-3">
            @if($pendingSubscriptions > 0)
                <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-800 dark:text-amber-200 rounded-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <span class="font-medium text-sm sm:text-base">{{ $pendingSubscriptions }} abonnement(s) en attente de validation</span>
                    <a href="{{ route('admin.subscriptions.index') }}" class="text-xs sm:text-sm font-semibold underline">Voir</a>
                </div>
            @endif
            @if($pendingWithdrawals > 0)
                <div class="p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700 text-rose-800 dark:text-rose-200 rounded-lg flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <span class="font-medium text-sm sm:text-base">{{ $pendingWithdrawals }} demande(s) de retrait en attente</span>
                    <a href="{{ route('admin.withdrawals.index') }}" class="text-xs sm:text-sm font-semibold underline">Voir</a>
                </div>
            @endif
        </div>
    @endif

    <form method="get" action="{{ route('admin.dashboard') }}" class="mb-4 flex flex-col sm:flex-row sm:items-center gap-2">
        <div class="flex items-center gap-2 flex-1">
            <input type="date" name="start" value="{{ $start->format('Y-m-d') }}" class="flex-1 min-w-0 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-xs sm:text-sm">
            <span class="text-slate-400 text-xs sm:text-sm shrink-0">à</span>
            <input type="date" name="end" value="{{ $end->format('Y-m-d') }}" class="flex-1 min-w-0 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 rounded-lg px-3 py-2 text-xs sm:text-sm">
        </div>
        <button type="submit" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white text-xs sm:text-sm font-semibold py-2.5 px-4 rounded-lg transition cursor-pointer">
            Filtrer
        </button>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Chiffre d'affaires validé</p>
            <p class="text-2xl font-bold text-green-700">$ {{ number_format($kpi['financial']['total_revenue_usd'], 2) }}</p>
            <p class="text-xs text-slate-400 mt-1">Période : {{ $kpi['period']['start'] }} - {{ $kpi['period']['end'] }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Commandes validées</p>
            <p class="text-2xl font-bold text-white">{{ $kpi['orders']['validated'] }}</p>
            <p class="text-xs text-slate-500 mt-1">{{ $kpi['orders']['total'] }} total — {{ $kpi['orders']['cancelled'] }} annulées</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Profit estimé</p>
            <p class="text-2xl font-bold text-blue-700">$ {{ number_format($kpi['financial']['profit_estimate'], 2) }}</p>
            <p class="text-xs text-slate-400 mt-1">Marge 25% — Commissions déduites</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Commissions versées</p>
            <p class="text-2xl font-bold text-purple-700">$ {{ number_format($kpi['financial']['commissions_paid'], 2) }}</p>
            <p class="text-xs text-slate-400 mt-1">Retraits : $ {{ number_format($kpi['financial']['withdrawals_paid'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Coût moyen livraison</p>
            <p class="text-2xl font-bold text-amber-600">$ {{ number_format($kpi['financial']['avg_delivery_cost'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Taux d'annulation</p>
            <p class="text-2xl font-bold text-red-600">{{ $kpi['orders']['cancellation_rate'] }}%</p>
            <p class="text-xs text-slate-400 mt-1">{{ $kpi['orders']['lost'] }} commandes perdues</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Clients</p>
            <p class="text-2xl font-bold text-blue-700">{{ $totalClients }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Agents / Livreurs</p>
            <p class="text-2xl font-bold text-purple-700">{{ $totalAgents }} / {{ $totalLivreurs }}</p>
        </div>
    </div>

    <h2 class="text-lg font-semibold text-white mb-3 mt-2">Statistiques Abonnements</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Abonnements actifs</p>
            <p class="text-2xl font-bold text-green-600">{{ $kpi['subscriptions']['active'] }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Abonnements expirés</p>
            <p class="text-2xl font-bold text-red-600">{{ $kpi['subscriptions']['expired'] }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Renouvellements du mois</p>
            <p class="text-2xl font-bold text-blue-600">{{ $kpi['subscriptions']['renewals_this_month'] }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Revenus hebdomadaires</p>
            <p class="text-2xl font-bold text-green-700">$ {{ number_format($kpi['subscriptions']['weekly_revenue'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Revenus mensuels</p>
            <p class="text-2xl font-bold text-green-700">$ {{ number_format($kpi['subscriptions']['monthly_revenue'], 2) }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Repas livrés</p>
            <p class="text-2xl font-bold text-white">{{ $kpi['subscriptions']['meals_delivered'] }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Repas non récupérés</p>
            <p class="text-2xl font-bold text-amber-600">{{ $kpi['subscriptions']['meals_not_picked_up'] }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Taux de renouvellement</p>
            <p class="text-2xl font-bold text-purple-600">{{ $kpi['subscriptions']['renewal_rate'] }}%</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Nouveaux abonnés</p>
            <p class="text-2xl font-bold text-blue-700">{{ $kpi['subscriptions']['new_subscribers'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="rounded-[2rem] border border-white/10 bg-white/[0.07] p-6 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <h2 class="text-lg font-semibold text-white mb-4">Zones les plus rentables</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/45"><tr><th class="px-4 py-2 text-left text-slate-400">Adresse</th><th class="px-4 py-2 text-left text-slate-400">Commandes</th><th class="px-4 py-2 text-left text-slate-400">Revenu</th></tr></thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($kpi['profitable_zones'] as $zone)
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ Str::limit($zone->delivery_address, 40) }}</td>
                                <td class="px-4 py-2">{{ $zone->orders_count }}</td>
                                <td class="px-4 py-2">$ {{ number_format($zone->total_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-2 text-slate-400" colspan="3">Aucune donnée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-[2rem] border border-white/10 bg-white/[0.07] p-6 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <h2 class="text-lg font-semibold text-white mb-4">Commandes par statut</h2>
            @php
                $statusLabels = ['pending' => 'En attente','confirmed' => 'Confirmée','preparing' => 'En préparation','delivering' => 'En livraison','delivered' => 'Livrée','cancelled' => 'Annulée'];
                $statusColors = ['pending' => 'bg-yellow-500','confirmed' => 'bg-blue-500','preparing' => 'bg-indigo-500','delivering' => 'bg-purple-500','delivered' => 'bg-green-500','cancelled' => 'bg-red-500'];
                $maxCount = max(!empty($ordersByStatus) ? max($ordersByStatus) : 0, 1);
            @endphp
            <div class="space-y-3">
                @foreach($statusLabels as $status => $label)
                    @php $count = $ordersByStatus[$status] ?? 0; @endphp
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-400 w-24">{{ $label }}</span>
                        <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $statusColors[$status] }} rounded-full" style="width: {{ ($count / $maxCount) * 100 }}%;"></div>
                        </div>
                        <span class="text-sm font-medium w-8 text-right">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Commandes récentes</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-green-600 hover:underline">Tout voir</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/45"><tr><th class="px-4 py-2 text-left text-slate-400">Code</th><th class="px-4 py-2 text-left text-slate-400">Agent</th><th class="px-4 py-2 text-left text-slate-400">Total</th><th class="px-4 py-2 text-left text-slate-400">Statut</th></tr></thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ $order->code }}</td>
                                <td class="px-4 py-2">{{ $order->agent->name ?? '-' }}</td>
                                <td class="px-4 py-2">$ {{ number_format($order->total_amount, 2) }}<br><span class="text-xs text-slate-500">{{ number_format($order->total_amount_fc, 0, ',', '.') }} FC</span></td>
                                <td class="px-4 py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $order->status_color_class }}">{{ $order->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-2 text-slate-400" colspan="4">Aucune commande</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Abonnements récents</h2>
                <a href="{{ route('admin.subscriptions.index') }}" class="text-sm text-green-600 hover:underline">Tout voir</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/45"><tr><th class="px-4 py-2 text-left text-slate-400">Client</th><th class="px-4 py-2 text-left text-slate-400">Type</th><th class="px-4 py-2 text-left text-slate-400">Statut</th></tr></thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($recentSubscriptions as $sub)
                            <tr>
                                <td class="px-4 py-2">{{ $sub->client->name ?? '-' }}</td>
                                <td class="px-4 py-2">{{ ucfirst($sub->type) }}</td>
                                <td class="px-4 py-2"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $sub->status_color_class }}">{{ $sub->status }}</span></td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-2 text-slate-400" colspan="3">Aucun abonnement</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <div class="px-6 py-4 border-b border-white/10">
                <h2 class="text-lg font-semibold text-white">Performance Agents</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/45"><tr><th class="px-6 py-3 text-left text-slate-400">Nom</th><th class="px-6 py-3 text-left text-slate-400">Commandes</th><th class="px-6 py-3 text-left text-slate-400">Commissions</th></tr></thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($kpi['top_agents'] as $agent)
                            <tr>
                                <td class="px-6 py-3 font-medium">{{ $agent->name }}</td>
                                <td class="px-6 py-3">{{ $agent->orders_as_agent_count }}</td>
                                <td class="px-6 py-3">$ {{ number_format($agent->commissions_sum_amount_usd ?? 0, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td class="px-6 py-3 text-slate-400" colspan="3">Aucun agent</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <div class="px-6 py-4 border-b border-white/10">
                <h2 class="text-lg font-semibold text-white">Top Agents (Global)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/45"><tr><th class="px-6 py-3 text-left text-slate-400">Nom</th><th class="px-6 py-3 text-left text-slate-400">Commandes validées</th><th class="px-6 py-3 text-left text-slate-400">Téléphone</th></tr></thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($kpi['top_agents'] as $agent)
                            <tr>
                                <td class="px-6 py-3 font-medium">{{ $agent->name }}</td>
                                <td class="px-6 py-3">{{ $agent->orders_as_agent_count }}</td>
                                <td class="px-6 py-3 text-slate-400">{{ $agent->phone }}</td>
                            </tr>
                        @empty
                            <tr><td class="px-6 py-3 text-slate-400" colspan="3">Aucun agent</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
            </div>
        </div>
    </div>
</x-app-layout>

