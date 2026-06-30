<x-app-layout>
    @php $canWithdraw = $available >= 10; @endphp
    <div class="-m-4 lg:-m-8 min-h-screen overflow-hidden bg-slate-950 text-white">
        <div class="relative min-h-screen">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(34,197,94,0.24),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(250,204,21,0.16),transparent_34%)]"></div>
            <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px;"></div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-lg shadow-emerald-400/60"></span>
                            Espace agent
                        </div>
                        <h1 class="text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">Retraits</h1>
                        <p class="mt-2 max-w-2xl text-sm text-slate-300 sm:text-base">Demandez un retrait de vos commissions et suivez le traitement en temps réel.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 shadow-xl backdrop-blur-xl">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Minimum retrait</p>
                        <p class="mt-1 text-lg font-bold text-white">$ 10.00</p>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
                    <section class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] p-6 shadow-2xl shadow-black/30 backdrop-blur-2xl sm:p-8">
                        <div class="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-emerald-400/20 blur-3xl"></div>
                        <div class="relative z-10">
                            <p class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-300">Solde disponible</p>
                            <p class="mt-4 font-mono text-6xl font-black leading-none tracking-tight text-white sm:text-7xl">$ {{ number_format($available, 2) }}</p>
                            <p class="mt-4 text-sm text-slate-300">Montant actuellement disponible pour retrait.</p>
                            <div class="mt-6 rounded-2xl border {{ $canWithdraw ? 'border-emerald-300/20 bg-emerald-300/10 text-emerald-100' : 'border-rose-300/20 bg-rose-300/10 text-rose-100' }} p-4 text-sm font-semibold">
                                {{ $canWithdraw ? 'Vous pouvez demander un retrait maintenant.' : 'Solde insuffisant. Minimum requis : $ 10.00.' }}
                            </div>
                        </div>
                    </section>

                    <section class="rounded-[2rem] border border-white/10 bg-white/[0.07] p-6 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-8">
                        <h2 class="text-xl font-black text-white">Demander un retrait</h2>
                        <p class="mt-1 text-sm text-slate-400">Indiquez le montant USD à retirer.</p>
                        @if(!$canWithdraw)
                            <div class="mt-6 rounded-2xl border border-rose-300/20 bg-rose-300/10 p-5 text-sm text-rose-100">Solde insuffisant pour lancer une nouvelle demande.</div>
                        @else
                            <form method="POST" action="{{ route('agent.withdrawals.store') }}" class="mt-6 grid gap-4 sm:grid-cols-[1fr_auto]">
                                @csrf
                                <input type="number" step="0.01" name="amount_usd" min="10" max="{{ $available }}" required placeholder="Montant USD (min 10)" class="rounded-2xl border-white/10 bg-slate-950/60 px-4 py-3 text-white shadow-sm focus:border-emerald-400 focus:ring-emerald-400">
                                <button type="submit" class="rounded-2xl bg-emerald-500 px-6 py-3 font-black text-slate-950 shadow-lg shadow-emerald-500/20 transition hover:bg-emerald-400">Demander</button>
                            </form>
                        @endif
                    </section>
                </div>

                <section class="mt-6 overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/25 backdrop-blur-2xl">
                    <div class="flex flex-col gap-2 border-b border-white/10 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <h2 class="text-lg font-black text-white">Historique des retraits</h2>
                            <p class="text-sm text-slate-400">Suivi de vos demandes envoyées</p>
                        </div>
                    </div>
                    <div class="divide-y divide-white/10">
                        @forelse($withdrawals as $w)
                            <div class="grid gap-4 px-5 py-4 transition hover:bg-white/[0.05] sm:grid-cols-4 sm:items-center sm:px-6">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Montant USD</p>
                                    <p class="mt-1 text-lg font-black text-white">$ {{ number_format($w->amount_usd, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Montant FC</p>
                                    <p class="mt-1 text-lg font-black text-emerald-300">{{ number_format($w->amount_fc, 0) }} FC</p>
                                </div>
                                <div><span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $w->status_color_class }}">{{ $w->status }}</span></div>
                                <div class="text-sm text-slate-400 sm:text-right">{{ $w->created_at?->format('d/m/Y') }}</div>
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
