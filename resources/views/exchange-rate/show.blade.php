@extends('layouts.app')

@section('title', 'Taux de Change - Green Express')

@section('content')
<style>
@keyframes spin-gold {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
@keyframes pulse-gold {
    0%, 100% { opacity: 1; box-shadow: 0 0 20px rgba(234,179,8,0.3); }
    50% { opacity: 0.85; box-shadow: 0 0 40px rgba(234,179,8,0.6); }
}
@keyframes color-shift {
    0% { filter: hue-rotate(0deg); }
    50% { filter: hue-rotate(15deg); }
    100% { filter: hue-rotate(0deg); }
}
.gold-ring {
    animation: spin-gold 8s linear infinite, pulse-gold 3s ease-in-out infinite;
}
.gold-text-shimmer {
    background: linear-gradient(90deg, #fbbf24, #f59e0b, #d97706, #fbbf24);
    background-size: 200% auto;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: shimmer 3s linear infinite;
}
@keyframes shimmer {
    0% { background-position: 0% center; }
    100% { background-position: 200% center; }
}
.rate-bar {
    transition: height 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

<div class="-m-4 -mt-[calc(1rem+4rem+env(safe-area-inset-top))] lg:-m-8 lg:-mt-[calc(2rem+4rem+env(safe-area-inset-top))] min-h-screen bg-gradient-to-b from-emerald-950 via-emerald-900 to-emerald-950 text-white relative overflow-hidden">
    {{-- Motif subtil en arrière-plan --}}
    <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 32px 32px;"></div>

    <div class="relative z-10 flex flex-col items-center px-4 pt-8 pb-12 min-h-screen">
        {{-- En-tête --}}
        <div class="text-center mb-8">
            <div class="flex items-center justify-center gap-3 mb-2">
                <span class="text-2xl">🇺🇸</span>
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <span class="text-2xl">🇨🇩</span>
            </div>
            <h1 class="text-sm font-medium text-emerald-300 uppercase tracking-widest">Taux de Change</h1>
            <p class="text-xs text-emerald-400/70 mt-1">USD / Franc Congolais</p>
        </div>

        {{-- Cercle doré animé --}}
        <div class="relative mb-10">
            {{-- Anneau extérieur tournant --}}
            <div class="w-64 h-64 rounded-full gold-ring border-4 border-yellow-400/30 flex items-center justify-center relative"
                 style="background: conic-gradient(from 0deg, rgba(251,191,36,0.1), rgba(251,191,36,0.3), rgba(217,119,6,0.2), rgba(251,191,36,0.1));">
                {{-- Anneau intérieur --}}
                <div class="w-56 h-56 rounded-full border-2 border-yellow-500/20 flex items-center justify-center bg-emerald-950/80 backdrop-blur-sm shadow-2xl">
                    <div class="text-center">
                        <p class="text-xs text-emerald-400 uppercase tracking-wider mb-1">1 USD =</p>
                        <p class="text-4xl font-bold gold-text-shimmer font-mono tracking-tight">
                            {{ number_format($currentRate, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-yellow-400/80 font-medium mt-1">FC</p>
                    </div>
                </div>
            </div>

            {{-- Points décoratifs sur le cercle --}}
            <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1 w-3 h-3 bg-yellow-400 rounded-full shadow-lg shadow-yellow-400/50"></div>
            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1 w-3 h-3 bg-yellow-500 rounded-full shadow-lg shadow-yellow-500/50"></div>
            <div class="absolute left-0 top-1/2 -translate-x-1 -translate-y-1/2 w-2 h-2 bg-emerald-400 rounded-full"></div>
            <div class="absolute right-0 top-1/2 translate-x-1 -translate-y-1/2 w-2 h-2 bg-emerald-400 rounded-full"></div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4 w-full max-w-sm mb-8">
            @php
                $last = $history->last();
                $prev = $history->count() > 1 ? $history[$history->count() - 2] : null;
                $variation = $prev ? round((($last->rate - $prev->rate) / $prev->rate) * 100, 2) : 0;
            @endphp
            <div class="bg-white/5 backdrop-blur rounded-xl p-3 text-center border border-white/10">
                <p class="text-[10px] text-emerald-300/70 uppercase tracking-wider">Variation</p>
                <p class="text-lg font-bold {{ $variation >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ $variation >= 0 ? '+' : '' }}{{ $variation }}%
                </p>
            </div>
            <div class="bg-white/5 backdrop-blur rounded-xl p-3 text-center border border-white/10">
                <p class="text-[10px] text-emerald-300/70 uppercase tracking-wider">Moyenne</p>
                <p class="text-lg font-bold text-yellow-300">
                    {{ number_format($history->avg('rate'), 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-white/5 backdrop-blur rounded-xl p-3 text-center border border-white/10">
                <p class="text-[10px] text-emerald-300/70 uppercase tracking-wider">Dernière MàJ</p>
                <p class="text-lg font-bold text-white">
                    {{ $last ? $last->created_at->format('d/m') : '--' }}
                </p>
            </div>
        </div>

        {{-- Graphique historique SVG --}}
        @if($history->count() >= 2)
        <div class="w-full max-w-sm mb-8">
            <h2 class="text-xs text-emerald-300/70 uppercase tracking-wider mb-4 text-center">Historique ({{ $history->count() }} jours)</h2>
            <div class="bg-white/5 backdrop-blur rounded-2xl p-4 border border-white/10">
                @php
                    $rates = $history->pluck('rate')->toArray();
                    $minRate = min($rates);
                    $maxRate = max($rates);
                    $range = $maxRate - $minRate ?: 1;
                    $count = count($rates);
                    $width = 280;
                    $height = 120;
                    $paddingX = 10;
                    $paddingY = 15;
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
                            <stop offset="0%" stop-color="rgba(251,191,36,0.3)"/>
                            <stop offset="100%" stop-color="rgba(251,191,36,0)"/>
                        </linearGradient>
                    </defs>

                    {{-- Aire sous la courbe --}}
                    <polygon points="{{ $areaPoints }}" fill="url(#graphGrad)"/>

                    {{-- Ligne --}}
                    <polyline points="{{ $pointsStr }}" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

                    {{-- Points --}}
                    @foreach($rates as $i => $rate)
                        @php
                            $x = $paddingX + ($i / ($count - 1)) * $graphW;
                            $y = $paddingY + $graphH - (($rate - $minRate) / $range) * $graphH;
                        @endphp
                        <circle cx="{{ $x }}" cy="{{ $y }}" r="3" fill="#fbbf24" stroke="#064e3b" stroke-width="1.5"/>
                    @endforeach
                </svg>

                {{-- Légende X --}}
                <div class="flex justify-between mt-2 px-1">
                    @foreach($history as $item)
                        <span class="text-[9px] text-emerald-400/60">{{ $item->created_at->format('d/m') }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Info --}}
        <div class="text-center text-emerald-400/50 text-[10px] max-w-xs">
            Ce taux est utilisé pour toutes les conversions dans l'application Green Express.
            Mis à jour automatiquement par l'administrateur.
        </div>
    </div>
</div>
@endsection
