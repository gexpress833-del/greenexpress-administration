<x-app-layout>
    @php
        $canWithdraw = $available >= 10;
        $totalWithdrawn = $withdrawals->sum('amount_usd');
        $pendingCount = $withdrawals->where('status', 'pending')->count();
    @endphp
    <div class="-m-4 lg:-m-8 min-h-screen overflow-hidden bg-slate-950 text-white">
        <div class="relative min-h-screen">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(34,197,94,0.24),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(250,204,21,0.16),transparent_34%)]"></div>
            <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px;"></div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mb-6">
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">
                        <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-lg shadow-emerald-400/60"></span>
                        Espace agent
                    </div>
                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">Retraits</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300 sm:text-base">Retirez vos commissions et suivez l’historique de vos demandes.</p>
                </div>

                <section class="relative mb-6 overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] p-6 shadow-2xl shadow-black/30 backdrop-blur-2xl sm:p-8 lg:p-10">
                    <div class="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-emerald-400/20 blur-3xl"></div>
                    <div class="absolute -bottom-28 -left-20 h-72 w-72 rounded-full bg-yellow-400/10 blur-3xl"></div>

                    <div class="relative z-10">
                        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-300">Solde disponible pour retrait</p>
                                <p class="mt-3 font-mono text-6xl font-black leading-none tracking-tight text-white sm:text-7xl lg:text-8xl">$ {{ number_format($available, 2) }}</p>
                                <p class="mt-3 text-sm text-slate-300">Minimum requis : <span class="font-bold text-white">$ 10.00</span></p>
                            </div>

                            <div class="w-full max-w-md">
                                @if($canWithdraw)
                                    <form method="POST" action="{{ route('agent.withdrawals.store') }}" class="grid gap-3">
                                        @csrf
                                        <label class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Montant à retirer (USD)</label>
                                        <div class="flex gap-3">
                                            <input type="number" step="0.01" name="amount_usd" min="10" max="{{ $available }}" required placeholder="Ex: 25.00" class="flex-1 rounded-2xl border-white/10 bg-slate-950/60 px-4 py-3 text-white shadow-sm focus:border-emerald-400 focus:ring-emerald-400">
                                            <button type="submit" class="rounded-2xl bg-emerald-500 px-6 py-3 font-black text-slate-950 shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-400">Demander</button>
                                        </div>
                                        <p class="text-xs text-slate-500">Maximum autorisé : $ {{ number_format($available, 2) }}</p>
                                    </form>
                                @else
                                    <div class="rounded-2xl border border-rose-300/20 bg-rose-300/10 p-5">
                                        <p class="text-sm font-bold text-rose-100">Solde insuffisant</p>
                                        <p class="mt-1 text-xs text-rose-200/80">Vous avez besoin d’au moins $ 10.00 pour demander un retrait.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                <div class="mb-6 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4 shadow-xl backdrop-blur-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Total retiré</p>
                        <p class="mt-2 text-2xl font-black text-white">$ {{ number_format($totalWithdrawn, 2) }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4 shadow-xl backdrop-blur-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Demandes en cours</p>
                        <p class="mt-2 text-2xl font-black text-yellow-300">{{ $pendingCount }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4 shadow-xl backdrop-blur-2xl">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Seuil minimum</p>
                        <p class="mt-2 text-2xl font-black text-emerald-300">$ 10.00</p>
                    </div>
                </div>

                <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/25 backdrop-blur-2xl">
                    <div class="border-b border-white/10 px-5 py-5 sm:px-6">
                        <h2 class="text-lg font-black text-white">Historique des retraits</h2>
                        <p class="text-sm text-slate-400">Liste de vos demandes et leur statut</p>
                    </div>
                    <div class="divide-y divide-white/10">
                        @forelse($withdrawals as $w)
                            <div class="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-white/[0.05] sm:px-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-400/10 text-emerald-200 ring-1 ring-emerald-300/20">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-black text-white">$ {{ number_format($w->amount_usd, 2) }} <span class="text-sm font-medium text-slate-400">/</span> <span class="text-sm font-bold text-emerald-300">{{ number_format($w->amount_fc, 0) }} FC</span></p>
                                        <p class="text-xs text-slate-500">{{ $w->created_at?->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $w->status_color_class }}">{{ $w->status }}</span>
                            </div>
                        @empty
                            <div class="px-5 py-14 text-center text-slate-400">Aucun retrait pour le moment.</div>
                        @endforelse
                    </div>
                    <div class="border-t border-white/10 bg-slate-950/35 px-5 py-4 sm:px-6">{{ $withdrawals->links() }}</div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
