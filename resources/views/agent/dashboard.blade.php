<x-app-layout>
    <div class="-m-4 lg:-m-8 min-h-screen overflow-hidden bg-slate-950 text-white">
        <div class="relative min-h-screen">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(34,197,94,0.22),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,0.16),transparent_34%)]"></div>
            <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px;"></div>
            <div class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-white">Dashboard Agent</h1>
        <span class="text-sm text-slate-400">{{ now()->format('d/m/Y H:i') }}</span>
    </div>

    @if($pendingWithdrawals > 0)
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-800 dark:text-amber-200 rounded-lg flex items-center justify-between">
            <span class="font-medium">{{ $pendingWithdrawals }} demande(s) de retrait en cours de traitement</span>
            <a href="{{ route('agent.withdrawals.index') }}" class="text-sm font-semibold underline">Voir</a>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Commandes aujourd'hui</p>
            <p class="text-2xl font-bold text-green-700">{{ $todayOrders }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Total commandes validées</p>
            <p class="text-2xl font-bold text-white">{{ $totalOrders }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Points total</p>
            <p class="text-2xl font-bold text-blue-700">{{ $totalPoints }}</p>
            <p class="text-xs text-slate-400 mt-1">+{{ $todayPoints }} aujourd'hui</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Solde disponible</p>
            <p class="text-2xl font-bold text-purple-700">$ {{ number_format($availableBalance, 2) }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ number_format($availablePoints) }} points · minimum $ 5.00</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-sm text-slate-400">Bonus repas aujourd'hui</p>
            <p class="text-2xl font-bold text-pink-600">{{ $todayRewards }}</p>
            <p class="text-xs text-slate-400 mt-1">max 1/jour</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="rounded-[2rem] border border-white/10 bg-white/[0.07] p-6 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <h2 class="text-lg font-semibold text-white mb-4">Commandes des 7 derniers jours</h2>
            @php $maxWeekly = max(!empty($weeklyOrders) ? max($weeklyOrders) : 0, 1); @endphp
            <div class="flex items-end gap-3 h-40">
                @foreach($weeklyOrders as $day => $count)
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="w-full bg-blue-100 rounded-t" style="height: {{ $maxWeekly > 0 ? ($count / $maxWeekly) * 100 : 0 }}%;">
                            <div class="w-full h-full bg-blue-500 rounded-t opacity-80"></div>
                        </div>
                        <span class="text-xs text-slate-400">{{ $day }}</span>
                        <span class="text-[10px] text-slate-500">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-[2rem] border border-white/10 bg-white/[0.07] p-6 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <h2 class="text-lg font-semibold text-white mb-4">Conversion des points</h2>
            <p class="text-sm text-slate-300">Chaque commande validée vous rapporte 12 points.</p>
            <p class="mt-2 text-sm text-slate-400">1 point vaut $ 0,025. Le retrait est disponible dès 200 points, soit $ 5.00.</p>
            <a href="{{ route('agent.withdrawals.index') }}" class="mt-4 inline-flex rounded-xl bg-emerald-500 px-4 py-2 text-sm font-bold text-slate-950">Convertir mes points</a>
        </div>
    </div>

    @if($badges->count() > 0)
    <div class="rounded-[2rem] border border-white/10 bg-white/[0.07] p-6 shadow-2xl shadow-black/20 backdrop-blur-2xl mb-6">
        <h2 class="text-lg font-semibold text-white mb-4">Badges récents</h2>
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
        <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">Commandes récentes</h2>
                <a href="{{ route('agent.orders.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">+ Nouvelle commande</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/45"><tr><th class="px-4 py-2 text-left text-slate-400">Code</th><th class="px-4 py-2 text-left text-slate-400">Client</th><th class="px-4 py-2 text-left text-slate-400">Total</th><th class="px-4 py-2 text-left text-slate-400">Statut</th></tr></thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ $order->code }}</td>
                                <td class="px-4 py-2">{{ $order->client_name }}</td>
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
            <div class="px-6 py-4 border-b border-white/10">
                <h2 class="text-lg font-semibold text-white">Top Clients</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-950/45"><tr><th class="px-4 py-2 text-left text-slate-400">Client</th><th class="px-4 py-2 text-left text-slate-400">Commandes</th><th class="px-4 py-2 text-left text-slate-400">Total</th></tr></thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($topClients as $client)
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ $client->client_name }}</td>
                                <td class="px-4 py-2">{{ $client->orders_count }}</td>
                                <td class="px-4 py-2">$ {{ number_format($client->total_spent, 2) }}<br><span class="text-xs text-slate-500">{{ number_format($client->total_spent_fc, 0, ',', '.') }} FC</span></td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-2 text-slate-400" colspan="3">Aucun client</td></tr>
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

