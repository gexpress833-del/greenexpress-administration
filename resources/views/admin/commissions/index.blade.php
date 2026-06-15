<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Commissions</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Agent</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Commande</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Points</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">USD</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">FC</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($commissions as $com)
                        <tr>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $com->agent->name }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $com->order->code }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $com->type }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $com->points }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">$ {{ number_format($com->amount_usd, 2) }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ number_format($com->amount_fc, 0) }} FC</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $com->created_at?->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="7">Aucune commission</td>
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
