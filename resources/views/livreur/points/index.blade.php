<x-app-layout>
    <style>
        @keyframes points-float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        @keyframes points-shimmer {
            0% { background-position: 0% center; }
            100% { background-position: 220% center; }
        }
        .points-orb {
            animation: points-float 5s ease-in-out infinite;
        }
        .points-shimmer {
            background: linear-gradient(90deg, #60a5fa, #22c55e, #facc15, #60a5fa);
            background-size: 220% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: points-shimmer 4s linear infinite;
        }
    </style>

    @php
        $maxWeekly = max(!empty($weeklyPoints) ? max($weeklyPoints) : 0, 1);
    @endphp

    <div class="-m-4 lg:-m-8 min-h-screen overflow-hidden bg-slate-950 text-white">
        <div class="relative min-h-screen">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(59,130,246,0.24),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(34,197,94,0.18),transparent_34%)]"></div>
            <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px;"></div>

            <div class="relative z-10 mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                <div class="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-blue-400/20 bg-blue-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-blue-200 shadow-lg shadow-blue-950/40">
                            <span class="h-2 w-2 rounded-full bg-emerald-400 shadow-lg shadow-emerald-400/60"></span>
                            Programme actif
                        </div>
                        <h1 class="text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">Mes points</h1>
                        <p class="mt-2 max-w-2xl text-sm text-slate-300 sm:text-base">Suivez vos points gagnés grâce à vos livraisons validées.</p>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-[1.05fr_0.95fr]">
                    <section class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/30 backdrop-blur-2xl sm:p-8 lg:p-10">
                        <div class="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-blue-400/20 blur-3xl"></div>
                        <div class="absolute -bottom-28 -left-20 h-72 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>

                        <div class="relative z-10 flex h-full flex-col justify-between gap-8">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-300">Solde disponible</p>
                                    <p class="mt-1 text-sm text-slate-300">Points cumulés par vos livraisons</p>
                                </div>
                                <div class="rounded-2xl bg-blue-400/10 p-3 text-blue-200 ring-1 ring-blue-300/20">
                                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.519 4.674c.3.921-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.519-4.674a1 1 0 00-.363-1.118L3.08 10.1c-.783-.57-.38-1.81.588-1.81h4.915a1 1 0 00.95-.69l1.516-4.674z"/></svg>
                                </div>
                            </div>

                            <div class="points-orb mx-auto flex w-full max-w-lg flex-col items-center justify-center rounded-[2rem] border border-blue-300/15 bg-slate-950/55 p-6 text-center shadow-2xl shadow-blue-950/50 sm:p-8">
                                <p class="text-sm font-bold uppercase tracking-[0.22em] text-slate-400">Total points</p>
                                <p class="points-shimmer mt-3 font-mono text-6xl font-black leading-none tracking-tight sm:text-7xl lg:text-8xl">{{ number_format($totalPoints) }}</p>
                            </div>

                            <div class="grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4">
                                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Aujourd'hui</p>
                                    <p class="mt-2 text-2xl font-black text-emerald-300">+{{ $todayPoints }}</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4">
                                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Entrées</p>
                                    <p class="mt-2 text-2xl font-black text-white">{{ $pointsHistory->total() }}</p>
                                    <p class="mt-1 text-xs text-slate-400">historique</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="grid gap-6">
                        <div class="rounded-[2rem] border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-6">
                            <div class="mb-5 flex items-center justify-between gap-4">
                                <div>
                                    <h2 class="text-lg font-black text-white">Activité récente</h2>
                                    <p class="text-sm text-slate-400">Points gagnés sur les 7 derniers jours</p>
                                </div>
                                <span class="rounded-full bg-blue-400/10 px-3 py-1 text-xs font-bold text-blue-200 ring-1 ring-blue-300/20">7 jours</span>
                            </div>
                            <div class="flex h-52 items-end gap-3 rounded-2xl border border-white/10 bg-slate-950/35 p-4">
                                @foreach($weeklyPoints as $day => $count)
                                    <div class="flex h-full flex-1 flex-col items-center justify-end gap-2">
                                        <span class="text-xs font-black text-white">{{ $count }}</span>
                                        <div class="relative w-full overflow-hidden rounded-t-2xl bg-white/10" style="height: {{ $maxWeekly > 0 ? max(($count / $maxWeekly) * 100, 8) : 8 }}%;">
                                            <div class="absolute inset-0 rounded-t-2xl bg-gradient-to-t from-blue-500 via-emerald-400 to-yellow-300 shadow-lg shadow-emerald-400/20"></div>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $day }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="rounded-[2rem] border border-emerald-300/15 bg-emerald-300/10 p-5 text-sm leading-relaxed text-emerald-50 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-6">
                            <div class="mb-4 flex items-center gap-3">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-300/15 text-emerald-200 ring-1 ring-emerald-200/20">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" /></svg>
                                </div>
                                <div>
                                    <p class="font-black text-white">Comment ça marche ?</p>
                                    <p class="text-xs text-emerald-200/70">Règles de calcul des points</p>
                                </div>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-1">
                                <div class="rounded-2xl border border-white/10 bg-slate-950/25 p-3">
                                    <p class="mb-1 text-xs font-black text-emerald-200">01</p>
                                    <p>7 points par livraison validée par le client (QR code ou code de validation)</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <section class="mt-6 overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/25 backdrop-blur-2xl">
                    <div class="flex flex-col gap-2 border-b border-white/10 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <h2 class="text-lg font-black text-white">Historique des points</h2>
                            <p class="text-sm text-slate-400">Détail de vos points gagnés</p>
                        </div>
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-slate-200">{{ $pointsHistory->total() }} entrées</span>
                    </div>
                    <div class="divide-y divide-white/10">
                        @forelse($pointsHistory as $point)
                            <div class="flex flex-col gap-4 px-5 py-4 transition hover:bg-white/[0.05] sm:flex-row sm:items-center sm:justify-between sm:px-6">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-blue-400/10 text-blue-200 ring-1 ring-blue-300/20">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-base font-black {{ $point->points >= 0 ? 'text-white' : 'text-red-300' }}">{{ $point->points >= 0 ? '+' : '' }}{{ $point->points }} points</p>
                                        <p class="mt-1 text-sm text-slate-300">{{ $point->description }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $point->created_at?->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                @if($point->delivery)
                                    <div class="flex items-center justify-between gap-4 text-left sm:flex-col sm:items-end sm:text-right">
                                        <a href="{{ route('livreur.deliveries.show', $point->delivery) }}" class="rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs font-bold text-slate-200 transition hover:border-blue-300/40 hover:text-blue-200">Voir livraison</a>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="px-5 py-14 text-center sm:px-6">
                                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-3xl bg-white/10 text-slate-300 ring-1 ring-white/10">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <p class="text-base font-bold text-white">Aucun point gagné pour le moment.</p>
                                <p class="mt-1 text-sm text-slate-400">Validez des livraisons pour commencer à gagner.</p>
                            </div>
                        @endforelse
                    </div>
                    @if($pointsHistory->hasPages())
                        <div class="border-t border-white/10 bg-slate-950/35 px-5 py-4 sm:px-6">
                            {{ $pointsHistory->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
