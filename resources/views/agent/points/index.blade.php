<x-app-layout>
    <div class="max-w-3xl mx-auto">
        {{-- Hero Card inspired by mobile app --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-xl mb-6">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>

            <div class="relative p-6 sm:p-8">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-blue-100 text-sm font-medium">Solde disponible</p>
                    <div class="flex items-center gap-1 bg-white/20 rounded-full px-3 py-1 text-xs font-semibold">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        Actif
                    </div>
                </div>

                <div class="mb-6">
                    <p class="text-4xl sm:text-5xl font-bold tracking-tight">{{ number_format($availablePoints) }}</p>
                    <p class="text-blue-200 text-sm mt-1">points disponibles</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="bg-white/15 backdrop-blur rounded-2xl p-4 flex-1">
                        <p class="text-blue-100 text-xs mb-1">Valeur disponible</p>
                        <p class="text-xl font-bold">$ {{ number_format($availableBalance, 2) }}</p>
                        <p class="text-xs text-blue-200 mt-0.5">{{ number_format($availableBalanceFc, 0, '.', '') }} FC</p>
                    </div>
                    <div class="bg-white/15 backdrop-blur rounded-2xl p-4 flex-1">
                        <p class="text-blue-100 text-xs mb-1">Aujourd'hui</p>
                        <p class="text-xl font-bold">+{{ $todayPoints }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Récapitulatif du solde --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">Récapitulatif du solde</h2>
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total accumulé</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-gray-100">$ {{ number_format($totalValueUsd, 2) }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ number_format($totalValueFc, 0, '.', '') }} FC</p>
                </div>
                <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Déjà retiré</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-gray-100">$ {{ number_format($totalWithdrawn, 2) }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ number_format($totalWithdrawnFc, 0, '.', '') }} FC</p>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Disponible</p>
                    <p class="text-lg font-bold text-gray-800 dark:text-gray-100">$ {{ number_format($availableBalance, 2) }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ number_format($availableBalanceFc, 0, '.', '') }} FC</p>
                </div>
            </div>
        </div>

        {{-- Weekly activity chart --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Activité des 7 derniers jours</h2>
                <span class="text-xs text-gray-400 dark:text-gray-500">Points gagnés</span>
            </div>
            @php $maxWeekly = max(!empty($weeklyPoints) ? max($weeklyPoints) : 0, 1); @endphp
            <div class="flex items-end gap-2 h-32">
                @foreach($weeklyPoints as $day => $count)
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span class="text-[10px] font-medium text-gray-600 dark:text-gray-300">{{ $count }}</span>
                        <div class="w-full bg-blue-100 dark:bg-blue-900/30 rounded-t-lg relative overflow-hidden" style="height: {{ $maxWeekly > 0 ? ($count / $maxWeekly) * 100 : 0 }}%;">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-blue-500 to-blue-400 rounded-t-lg opacity-90" style="height: 100%;"></div>
                        </div>
                        <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase">{{ $day }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 gap-3 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="w-8 h-8 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center mb-2">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400">Points aujourd'hui</p>
                <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ $todayPoints }}</p>
                <p class="text-[10px] text-gray-400 dark:text-gray-500">~$ {{ number_format($todayValueUsd, 2) }}</p>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <x-withdrawal-progress :available="$availableBalance" :minRequired="$minWithdrawal" :availableFc="$availableBalanceFc" :minRequiredFc="$minWithdrawalFc" :points="$totalPoints" label="Solde retirable" />
            </div>
        </div>

        {{-- How it works --}}
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-2xl p-4 mb-6">
            <p class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">Comment ça marche ?</p>
            <p class="text-xs text-blue-700 dark:text-blue-300 mb-2">Les points sont calculés par tranche de 5 000 FC du montant total de la commande (pas par repas).</p>
            <ul class="text-xs text-blue-700 dark:text-blue-300 space-y-1">
                <li class="flex items-start gap-2">
                    <span class="w-4 h-4 rounded-full bg-blue-200 dark:bg-blue-800 flex items-center justify-center text-[10px] font-bold mt-0.5 shrink-0">1</span>
                    <span>1 000 – 4 999 FC : <strong>3 pts</strong> (~$ 0.08)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-4 h-4 rounded-full bg-blue-200 dark:bg-blue-800 flex items-center justify-center text-[10px] font-bold mt-0.5 shrink-0">2</span>
                    <span>5 000 – 9 999 FC : <strong>6 pts</strong> (~$ 0.15)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-4 h-4 rounded-full bg-blue-200 dark:bg-blue-800 flex items-center justify-center text-[10px] font-bold mt-0.5 shrink-0">3</span>
                    <span>10 000 – 14 999 FC : <strong>9 pts</strong> (~$ 0.23)</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-4 h-4 rounded-full bg-blue-200 dark:bg-blue-800 flex items-center justify-center text-[10px] font-bold mt-0.5 shrink-0">4</span>
                    <span>15 000 FC et + : <strong>12 pts</strong> (~$ 0.30)</span>
                </li>
            </ul>
            <p class="text-[10px] text-blue-600 dark:text-blue-400 mt-2 italic">Exemple : une commande de 2 500 FC + 10 000 FC = 12 500 FC → 9 points</p>
        </div>

        {{-- History --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Historique des points</h2>
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $pointsHistory->total() }} entrées</span>
            </div>
            <div class="divide-y divide-gray-50 dark:divide-gray-700">
                @forelse($pointsHistory as $point)
                    <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-100">+{{ $point->points }} points</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $point->description }}</p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ $point->earned_at?->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-blue-600 dark:text-blue-400">$ {{ number_format($point->value_usd, 2) }}</p>
                            @if($point->order)
                                <a href="{{ route('agent.orders.show', $point->order) }}" class="text-[10px] text-gray-400 dark:text-gray-500 hover:text-blue-500">Voir commande</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center">
                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aucun point gagné pour le moment.</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Validez des commandes pour commencer à gagner.</p>
                    </div>
                @endforelse
            </div>
            @if($pointsHistory->hasPages())
                <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
                    {{ $pointsHistory->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
