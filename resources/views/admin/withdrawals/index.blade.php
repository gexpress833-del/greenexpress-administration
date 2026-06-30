<x-app-layout>
    <div class="-m-4 lg:-m-8 min-h-screen overflow-hidden bg-slate-950 text-white">
        <div class="relative min-h-screen">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(244,63,94,0.22),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(34,197,94,0.16),transparent_34%)]"></div>
            <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px;"></div>
            <div class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-rose-400/20 bg-rose-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-rose-200">Administration</div>
                        <h1 class="text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">Retraits agents</h1>
                        <p class="mt-2 max-w-2xl text-sm text-slate-300 sm:text-base">Validez ou rejetez les demandes de retrait depuis une interface claire et rapide.</p>
                    </div>
                    <span class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm font-bold text-slate-200 shadow-xl backdrop-blur-xl">{{ $withdrawals->total() }} demande(s)</span>
                </div>

                <section class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/25 backdrop-blur-2xl">
                    <div class="divide-y divide-white/10">
                        @forelse($withdrawals as $w)
                            <div class="grid gap-4 px-5 py-5 transition hover:bg-white/[0.05] lg:grid-cols-[1.2fr_1fr_1fr_1fr_1.2fr] lg:items-center sm:px-6">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">Agent</p>
                                    <p class="mt-1 text-base font-black text-white">{{ $w->agent->name }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">USD</p>
                                    <p class="mt-1 text-lg font-black text-white">$ {{ number_format($w->amount_usd, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-500">FC</p>
                                    <p class="mt-1 text-lg font-black text-emerald-300">{{ number_format($w->amount_fc, 0) }} FC</p>
                                </div>
                                <div><span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $w->status_color_class }}">{{ $w->status }}</span><p class="mt-2 text-xs text-slate-500">{{ $w->created_at?->format('d/m/Y') }}</p></div>
                                <div class="flex flex-wrap gap-2 lg:justify-end">
                                    @if($w->status === 'pending')
                                        <form method="POST" action="{{ route('admin.withdrawals.update', $w) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="rounded-full bg-emerald-500 px-4 py-2 text-xs font-black text-slate-950 transition hover:bg-emerald-400">Approuver</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.withdrawals.update', $w) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="rounded-full border border-rose-300/30 bg-rose-400/10 px-4 py-2 text-xs font-black text-rose-200 transition hover:bg-rose-400/20">Rejeter</button>
                                        </form>
                                    @else
                                        <span class="text-sm text-slate-500">Traité</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-14 text-center text-slate-400">Aucune demande de retrait.</div>
                        @endforelse
                    </div>
                    <div class="border-t border-white/10 bg-slate-950/35 px-5 py-4 sm:px-6">{{ $withdrawals->links() }}</div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
