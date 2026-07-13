<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mes réclamations</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/50"><tr><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Commande</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Type</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Statut</th><th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400">Date</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($complaints as $complaint)
                        <tr>
                            <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $complaint->order->code ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $complaint->type_label }}</td>
                            <td class="px-4 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $complaint->status_color_class }}">{{ $complaint->status }}</span></td>
                            <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $complaint->created_at?->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-3 text-gray-500 dark:text-gray-400" colspan="4">Aucune réclamation</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">{{ $complaints->links() }}</div>
    </div>
</x-app-layout>
