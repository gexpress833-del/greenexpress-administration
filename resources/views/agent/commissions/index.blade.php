<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mes gains</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Récapitulatif de vos points et commissions sur toutes vos commandes validées.</p>
    </div>

    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg text-sm text-blue-800 dark:text-blue-200">
        <p class="font-semibold mb-1">Comment ça marche ?</p>
        <ul class="list-disc list-inside space-y-1">
            <li><strong>Points</strong> : vous gagnez 12 points par commande livrée et validée par le client. Ces points représentent votre activité.</li>
            <li><strong>Commissions</strong> : ce sont les sommes réelles gagnées (en USD et en FC) sur vos commandes. Elles sont versées sur votre solde.</li>
            <li>Vous pouvez demander un retrait dès que votre solde atteint le minimum requis.</li>
        </ul>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Points de fidélité gagnés</p>
            <p class="text-2xl font-bold text-blue-700">{{ $totalPoints }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">12 pts par commande validée</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Commissions totales (USD)</p>
            <p class="text-2xl font-bold text-green-700">$ {{ number_format($totalUsd, 2) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Somme réelle gagnée en dollars</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-100 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400">Commissions totales (FC)</p>
            <p class="text-2xl font-bold text-purple-700">{{ number_format($totalFc, 0) }} FC</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Équivalent en Francs Congolais</p>
        </div>
    </div>

    <div class="mb-6">
        <a href="{{ route('agent.withdrawals.index') }}" class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
            Voir mes retraits
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Historique de vos gains</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type de gain</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Points</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Montant USD</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Montant FC</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Détail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($commissions as $com)
                        <tr>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100 whitespace-nowrap">{{ $com->created_at?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $com->type }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $com->points }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">$ {{ number_format($com->amount_usd, 2) }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ number_format($com->amount_fc, 0) }} FC</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $com->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="6">Aucun gain pour le moment. Validez des commandes pour commencer à gagner.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $commissions->links() }}
        </div>
    </div>
</x-app-layout>
