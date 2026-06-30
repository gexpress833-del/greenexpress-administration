@extends('layouts.app')

@section('title', 'Taux de Change - Green Express')

@section('content')
<style>
@keyframes soft-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}
@keyframes glow-line {
    0%, 100% { opacity: .45; transform: scaleX(.92); }
    50% { opacity: 1; transform: scaleX(1); }
}
@keyframes shimmer {
    0% { background-position: 0% center; }
    100% { background-position: 200% center; }
}
.exchange-orb {
    animation: soft-float 5s ease-in-out infinite;
}
.exchange-shimmer {
    background: linear-gradient(90deg, #22c55e, #facc15, #16a34a, #22c55e);
    background-size: 220% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: shimmer 4s linear infinite;
}
.exchange-glow-line {
    animation: glow-line 3s ease-in-out infinite;
}
.rate-bar {
    transition: height 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

@php
    $last = $history->last();
    $prev = $history->count() > 1 ? $history[$history->count() - 2] : null;
    $variation = $prev ? round((($last->rate - $prev->rate) / $prev->rate) * 100, 2) : 0;
    $average = $history->count() ? $history->avg('rate') : $currentRate;
@endphp

<div class="-m-4 -mt-[calc(1rem+4rem+env(safe-area-inset-top))] lg:-m-8 lg:-mt-[calc(2rem+4rem+env(safe-area-inset-top))] min-h-screen overflow-hidden bg-slate-950 text-white">
    {{-- Motif subtil en arrière-plan --}}
    <div class="relative min-h-screen">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(34,197,94,0.28),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(250,204,21,0.18),transparent_32%)]"></div>
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px;"></div>

        <div class="relative z-10 mx-auto flex min-h-screen w-full max-w-7xl flex-col px-4 pb-10 sm:px-6 lg:px-8" style="padding-top: calc(1.25rem + 4rem + env(safe-area-inset-top));">
            {{-- En-tête --}}
            <div class="mb-6 flex flex-col gap-4 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200 shadow-lg shadow-emerald-950/40">
                        <span>🇺🇸</span>
                        <span class="text-emerald-400">USD</span>
                        <span class="text-slate-500">/</span>
                        <span class="text-yellow-300">CDF</span>
                        <span>🇨🇩</span>
                    </div>
                    <h1 class="text-3xl font-black tracking-tight sm:text-4xl lg:text-5xl">Taux de change</h1>
                    <p class="mt-2 max-w-2xl text-sm text-slate-300 sm:text-base">Suivi du taux USD vers Franc Congolais utilisé pour les conversions Green Express.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-sm text-slate-200 shadow-xl backdrop-blur-xl">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Dernière mise à jour</p>
                    <p class="mt-1 text-lg font-bold text-white">{{ $last ? $last->created_at->format('d/m/Y') : '--' }}</p>
                </div>
            </div>

            <div class="grid flex-1 gap-6 lg:grid-cols-[1.05fr_0.95fr] lg:items-stretch">
                {{-- Cercle doré animé --}}
                <section class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/30 backdrop-blur-2xl sm:p-8 lg:p-10">
                    <div class="absolute -right-24 -top-24 h-64 w-64 rounded-full bg-emerald-400/20 blur-3xl"></div>
                    <div class="absolute -bottom-28 -left-20 h-72 w-72 rounded-full bg-yellow-400/10 blur-3xl"></div>

                    <div class="relative z-10 flex h-full flex-col justify-between gap-8">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.25em] text-emerald-300">Conversion actuelle</p>
                                <p class="mt-1 text-sm text-slate-300">1 Dollar américain équivaut à</p>
                            </div>
                            <div class="rounded-2xl bg-emerald-400/10 p-3 text-emerald-200 ring-1 ring-emerald-300/20">
                                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        <div class="exchange-orb mx-auto flex w-full max-w-lg flex-col items-center justify-center rounded-[2rem] border border-emerald-300/15 bg-slate-950/55 p-6 text-center shadow-2xl shadow-emerald-950/50 sm:p-8">
                            <div class="mb-4 flex items-center gap-3 text-sm font-bold uppercase tracking-[0.22em] text-slate-400">
                                <span>1 USD</span>
                                <span class="h-px w-10 bg-emerald-400/60"></span>
                                <span>CDF</span>
                            </div>
                            <p class="exchange-shimmer font-mono text-6xl font-black leading-none tracking-tight sm:text-7xl lg:text-8xl">
                                {{ number_format($currentRate, 0, ',', '.') }}
                            </p>
                            <div class="exchange-glow-line mt-5 h-1 w-48 rounded-full bg-gradient-to-r from-transparent via-emerald-300 to-transparent"></div>
                            <p class="mt-5 max-w-sm text-sm leading-relaxed text-slate-300">Taux de référence appliqué à l'ensemble des conversions effectuées sur cette plateforme.</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3">
                            {{-- Stats --}}
                            <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4">
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Variation</p>
                                <p class="mt-2 text-2xl font-black {{ $variation >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">{{ $variation >= 0 ? '+' : '' }}{{ $variation }}%</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4">
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Moyenne</p>
                                <p class="mt-2 text-2xl font-black text-yellow-300">{{ number_format($average, 0, ',', '.') }}</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-white/[0.07] p-4">
                                <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Devise</p>
                                <p class="mt-2 text-2xl font-black text-white">FC</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-6">
                    {{-- Graphique historique SVG --}}
                    <div class="rounded-[2rem] border border-white/10 bg-white/[0.07] p-5 shadow-2xl shadow-black/25 backdrop-blur-2xl sm:p-6">
                        <div class="mb-5 flex items-center justify-between gap-4">
                            <div>
                                <h2 class="text-lg font-black text-white">Historique</h2>
                                <p class="text-sm text-slate-400">{{ $history->count() }} dernières valeurs enregistrées</p>
                            </div>
                            <div class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-bold text-emerald-200 ring-1 ring-emerald-300/20">7 jours</div>
                        </div>

                        @if($history->count() >= 2)
                            @php
                                $rates = $history->pluck('rate')->toArray();
                                $minRate = min($rates);
                                $maxRate = max($rates);
                                $range = $maxRate - $minRate ?: 1;
                                $count = count($rates);
                                $width = 420;
                                $height = 190;
                                $paddingX = 18;
                                $paddingY = 22;
                                $graphW = $width - $paddingX * 2;
                                $graphH = $height - $paddingY * 2;

                                $points = [];
                                foreach ($rates as $i => $rate) {
                                    $x = $paddingX + ($i / ($count - 1)) * $graphW;
                                    $y = $paddingY + $graphH - (($rate - $minRate) / $range) * $graphH;
                                    $points[] = "$x,$y";
                                }
                                $pointsStr = implode(' ', $points);

                                // Area fill
                                $areaPoints = $pointsStr . " $width," . ($height - $paddingY) . " $paddingX," . ($height - $paddingY);
                            @endphp
                            <svg viewBox="0 0 {{ $width }} {{ $height }}" class="w-full" preserveAspectRatio="xMidYMid meet">
                                {{-- Dégradé de fond --}}
                                <defs>
                                    <linearGradient id="graphGrad" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="rgba(34,197,94,0.35)"/>
                                        <stop offset="100%" stop-color="rgba(34,197,94,0)"/>
                                    </linearGradient>
                                    <filter id="lineGlow" x="-20%" y="-20%" width="140%" height="140%">
                                        <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
                                        <feMerge>
                                            <feMergeNode in="coloredBlur"/>
                                            <feMergeNode in="SourceGraphic"/>
                                        </feMerge>
                                    </filter>
                                </defs>

                                {{-- Aire sous la courbe --}}
                                <polygon points="{{ $areaPoints }}" fill="url(#graphGrad)"/>

                                {{-- Ligne --}}
                                <polyline points="{{ $pointsStr }}" fill="none" stroke="#34d399" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" filter="url(#lineGlow)"/>

                                {{-- Points --}}
                                @foreach($rates as $i => $rate)
                                    @php
                                        $x = $paddingX + ($i / ($count - 1)) * $graphW;
                                        $y = $paddingY + $graphH - (($rate - $minRate) / $range) * $graphH;
                                    @endphp
                                    <circle cx="{{ $x }}" cy="{{ $y }}" r="5" fill="#facc15" stroke="#0f172a" stroke-width="2"/>
                                @endforeach
                            </svg>

                            {{-- Légende X --}}
                            <div class="mt-3 flex justify-between px-1">
                                @foreach($history as $item)
                                    <span class="text-[10px] font-semibold text-slate-400">{{ $item->created_at->format('d/m') }}</span>
                                @endforeach
                            </div>
                        @else
                            <div class="flex min-h-44 items-center justify-center rounded-2xl border border-dashed border-white/15 bg-slate-950/30 text-sm text-slate-400">Historique insuffisant pour afficher le graphique.</div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="rounded-[2rem] border border-yellow-300/15 bg-yellow-300/10 p-5 text-sm leading-relaxed text-yellow-50 shadow-xl shadow-black/20 backdrop-blur-xl sm:p-6">
                        <div class="mb-3 flex h-11 w-11 items-center justify-center rounded-2xl bg-yellow-300/15 text-yellow-200 ring-1 ring-yellow-200/20">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p>Actualisé automatiquement par La Direction. Les prix en francs congolais sont recalculés à partir du taux le plus récent enregistré dans l'administration.</p>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection
