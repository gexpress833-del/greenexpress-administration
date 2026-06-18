<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Retraits</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-8">
        <x-withdrawal-progress :available="$available" :minRequired="$minWithdrawal" :availableFc="$availableFc" :minRequiredFc="$minWithdrawalFc" label="Solde disponible" />
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Demander un retrait</h2>
        @if($available < $minWithdrawal)
            <p class="text-sm text-red-600 dark:text-red-400">Solde insuffisant. Minimum requis : $ {{ number_format($minWithdrawal, 2) }} ({{ number_format($minWithdrawalFc, 0, ',', '.') }} FC)</p>
        @else
            <form method="POST" action="{{ route('livreur.withdrawals.store') }}" class="flex flex-col sm:flex-row gap-3" x-data="{ loading: false }" @submit="loading = true">
                @csrf
                <input type="number" step="0.01" name="amount_usd" min="{{ $minWithdrawal }}" max="{{ $available }}" required placeholder="Montant USD (min {{ $minWithdrawal }})"
                       class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 read-only:opacity-60 read-only:cursor-not-allowed"
                       :readonly="loading">
                <button type="submit" :disabled="loading" class="bg-green-600 hover:bg-green-700 disabled:bg-green-500 text-white font-semibold py-2 px-6 rounded-lg transition flex items-center gap-2 disabled:cursor-not-allowed">
                    <template x-if="loading">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                        </svg>
                    </template>
                    <span x-text="loading ? 'Traitement...' : 'Demander'">Demander</span>
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Historique des retraits</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Montant USD</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Montant FC</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($withdrawals as $w)
                        <tr>
                            <td class="px-6 py-3">$ {{ number_format($w->amount_usd, 2) }}</td>
                            <td class="px-6 py-3">{{ number_format($w->amount_fc, 0) }} FC</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $w->status_color_class }}">
                                    {{ $w->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3">{{ $w->created_at?->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="4">Aucun retrait</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $withdrawals->links() }}
        </div>
    </div>
</x-app-layout>
