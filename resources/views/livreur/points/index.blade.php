<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mes points</h1>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Points disponibles</p>
            <p class="text-2xl font-bold text-green-700">{{ $availablePoints }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">$ {{ number_format($availableBalance, 2) }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ number_format($availableBalanceFc, 0, '.', '') }} FC</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Aujourd'hui</p>
            <p class="text-2xl font-bold text-green-700">{{ $todayPoints }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">$ {{ number_format($todayValueUsd, 2) }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ number_format($todayValueFc, 0, '.', '') }} FC</p>
        </div>
        <div class="col-span-2 lg:col-span-1">
            <x-withdrawal-progress :available="$availableBalance" :minRequired="$minWithdrawal" :availableFc="$availableBalanceFc" :minRequiredFc="$minWithdrawalFc" :points="$totalPoints" label="Solde retirable" />
        </div>
        <div class="col-span-2 lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700 flex items-center justify-center">
            <a href="{{ route('livreur.withdrawals.index') }}" class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                Retirer
            </a>
        </div>
    </div>

    {{-- Récapitulatif du solde --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 mb-8">
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

    {{-- Comment ça marche --}}
    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 rounded-xl p-5 mb-8">
        <p class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-2">Comment ça marche ?</p>
        <p class="text-xs text-amber-700 dark:text-amber-300 mb-2">Les points sont calculés selon le montant total de la commande livrée et validée (pas par repas).</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                <p class="text-base font-bold text-amber-700 dark:text-amber-300">4 pts</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">1 000 – 4 999 FC</p>
                <p class="text-[10px] text-gray-400 dark:text-gray-500">~$ 0.10</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                <p class="text-base font-bold text-amber-700 dark:text-amber-300">6 pts</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">5 000 – 9 999 FC</p>
                <p class="text-[10px] text-gray-400 dark:text-gray-500">~$ 0.15</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                <p class="text-base font-bold text-amber-700 dark:text-amber-300">8 pts</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">10 000 – 14 999 FC</p>
                <p class="text-[10px] text-gray-400 dark:text-gray-500">~$ 0.20</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-lg p-3 text-center">
                <p class="text-base font-bold text-amber-700 dark:text-amber-300">10 pts</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400">15 000 FC et +</p>
                <p class="text-[10px] text-gray-400 dark:text-gray-500">~$ 0.25</p>
            </div>
        </div>
        <p class="text-[11px] text-amber-600 dark:text-amber-400 italic">
            Exemple : une livraison de commande à 2 500 FC + 10 000 FC (total 12 500 FC) = 8 points.
        </p>
    </div>

    {{-- Graphique hebdomadaire --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Points des 7 derniers jours</h2>
        <div class="flex items-end gap-3 h-32">
            @foreach($weeklyPoints as $day => $pts)
                @php $max = max($weeklyPoints) ?: 1; $height = $max > 0 ? ($pts / $max) * 100 : 0; @endphp
                <div class="flex-1 flex flex-col items-center gap-1">
                    <div class="w-full bg-green-100 dark:bg-green-900/30 rounded-t-md relative" style="height: {{ $height }}%;">
                        @if($pts > 0)
                            <div class="absolute -top-5 left-1/2 -translate-x-1/2 text-xs font-bold text-green-700 dark:text-green-300">{{ $pts }}</div>
                        @endif
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($day) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Historique --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Historique des points</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Commande</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Points</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Valeur</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($pointsHistory as $p)
                        <tr>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $p->order?->code ?? '-' }}</td>
                            <td class="px-6 py-3 font-bold text-green-700">+{{ $p->points }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">
                                $ {{ number_format($p->value_usd, 2) }}
                                <span class="text-xs text-gray-400 dark:text-gray-500 block">{{ number_format($p->value_usd * $exchangeRate, 0, '.', '') }} FC</span>
                            </td>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400">{{ $p->earned_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="4">Aucun point gagné pour le moment. Validez des livraisons pour accumuler des points.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $pointsHistory->links() }}
        </div>
    </div>
</x-app-layout>
