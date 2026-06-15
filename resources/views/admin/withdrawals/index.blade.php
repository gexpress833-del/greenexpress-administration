<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Retraits</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Bénéficiaire</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Rôle</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Montant USD</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Montant FC</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Statut</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Date</th>
                        <th class="px-6 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($withdrawals as $w)
                        <tr>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $w->beneficiary()?->name ?? 'Inconnu' }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $w->livreur_id ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-200' }}">
                                    {{ $w->beneficiary_role }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">$ {{ number_format($w->amount_usd, 2) }}</td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ number_format($w->amount_fc, 0) }} FC</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $w->status_color_class }}">
                                    {{ $w->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-gray-800 dark:text-gray-100">{{ $w->created_at?->format('d/m/Y') }}</td>
                            <td class="px-6 py-3">
                                @if($w->status === 'pending')
                                    <form method="POST" action="{{ route('admin.withdrawals.update', $w) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm mr-2">Approuver</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.withdrawals.update', $w) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">Rejeter</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-3 text-gray-500 dark:text-gray-400" colspan="7">Aucun retrait</td>
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
