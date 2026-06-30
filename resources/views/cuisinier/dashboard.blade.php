<x-app-layout>
    <div class="-m-4 lg:-m-8 min-h-screen overflow-hidden bg-slate-950 text-white">
        <div class="relative min-h-screen">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(34,197,94,0.22),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(59,130,246,0.16),transparent_34%)]"></div>
            <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px;"></div>
            <div class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <h1 class="text-xl sm:text-2xl font-bold text-white">Cuisine</h1>
        <span class="text-xs sm:text-sm text-slate-400">{{ now()->format('d/m/Y H:i') }}</span>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4 sm:p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-xs sm:text-sm text-slate-400">Aujourd'hui</p>
            <p class="text-xl sm:text-2xl font-bold text-green-700">{{ $todayOrders }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4 sm:p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-xs sm:text-sm text-slate-400">Ã€ prÃ©parer</p>
            <p class="text-xl sm:text-2xl font-bold text-amber-600">{{ $pendingOrders }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4 sm:p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-xs sm:text-sm text-slate-400">En prÃ©paration</p>
            <p class="text-xl sm:text-2xl font-bold text-blue-700">{{ $preparingOrders }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4 sm:p-5 shadow-2xl shadow-black/20 backdrop-blur-2xl">
            <p class="text-xs sm:text-sm text-slate-400">PrÃªtes</p>
            <p class="text-xl sm:text-2xl font-bold text-purple-700">{{ $readyOrders }}</p>
        </div>
    </div>

    {{-- Commandes rÃ©centes --}}
    <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/20 backdrop-blur-2xl">
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-b border-white/10 flex items-center justify-between">
            <h2 class="text-base sm:text-lg font-semibold text-white">Commandes en cours</h2>
            <a href="{{ route('cuisinier.orders.index') }}" class="text-xs sm:text-sm text-green-600 hover:underline font-medium">Tout voir</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-950/45">
                    <tr>
                        <th class="px-4 py-2 text-left text-slate-400">Code</th>
                        <th class="px-4 py-2 text-left text-slate-400 hidden sm:table-cell">Client</th>
                        <th class="px-4 py-2 text-left text-slate-400">Repas</th>
                        <th class="px-4 py-2 text-left text-slate-400">Statut</th>
                        <th class="px-4 py-2 text-left text-slate-400">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @forelse($recentOrders as $order)
                        <tr class="hover:bg-slate-950/45 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 font-medium">{{ $order->code }}</td>
                            <td class="px-4 py-3 hidden sm:table-cell">{{ $order->client_name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs">{{ $order->items->pluck('meal.name')->implode(', ') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $order->status === 'confirmed' ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300' : '' }}
                                    {{ $order->status === 'preparing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : '' }}
                                    {{ $order->status === 'delivering' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300' : '' }}">
                                    {{ $order->status === 'confirmed' ? 'Ã€ prÃ©parer' : '' }}
                                    {{ $order->status === 'preparing' ? 'En prÃ©paration' : '' }}
                                    {{ $order->status === 'delivering' ? 'PrÃªte' : '' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('cuisinier.orders.show', $order) }}" class="text-green-600 hover:text-green-800 text-xs sm:text-sm font-medium">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-3 text-slate-400 text-center" colspan="5">Aucune commande en cours</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
            </div>
        </div>
    </div>
</x-app-layout>

