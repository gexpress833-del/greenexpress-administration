@php
$progress = $minRequired > 0 ? min(100, round(($available / $minRequired) * 100, 1)) : 0;
$remaining = max(0, $minRequired - $available);
$radius = 48;
$circumference = 2 * M_PI * $radius;
$offset = $circumference - ($progress / 100) * $circumference;
$canWithdraw = $available >= $minRequired;
$color = $canWithdraw ? '#16a34a' : '#0ea5e9';
$colorClass = $canWithdraw ? 'text-green-600 dark:text-green-400' : 'text-sky-500 dark:text-sky-400';
$bgClass = $canWithdraw ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-sky-50 dark:bg-sky-900/20 border-sky-200 dark:border-sky-800';
@endphp

<div class="rounded-2xl border p-5 {{ $bgClass }}">
    <div class="flex items-center gap-5">
        {{-- Cercle SVG --}}
        <div class="relative w-28 h-28 shrink-0">
            <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="{{ $radius }}" fill="none" stroke="currentColor" stroke-width="8" class="text-gray-200 dark:text-gray-700" />
                <circle cx="60" cy="60" r="{{ $radius }}" fill="none" stroke="{{ $color }}" stroke-width="8"
                    stroke-linecap="round"
                    stroke-dasharray="{{ $circumference }}"
                    stroke-dashoffset="{{ $offset }}"
                    style="transition: stroke-dashoffset 1s ease;" />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-xl font-bold {{ $colorClass }}">{{ $progress }}%</span>
                <span class="text-[10px] text-gray-500 dark:text-gray-400">{{ $canWithdraw ? 'Dispo' : 'En cours' }}</span>
            </div>
        </div>

        {{-- Texte --}}
        <div class="flex-1">
            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $label ?? 'Retrait' }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                Solde : <span class="font-bold text-gray-700 dark:text-gray-200">$ {{ number_format($available, 2) }}</span>
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                {{ number_format($available * \App\Models\ExchangeRate::current(), 0, ',', '.') }} FC
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Min requis : $ {{ number_format($minRequired, 2) }}
            </p>
            <p class="text-xs text-gray-400 dark:text-gray-500">
                {{ number_format($minRequired * \App\Models\ExchangeRate::current(), 0, ',', '.') }} FC
            </p>

            @if($canWithdraw)
                <p class="text-xs font-semibold text-green-600 dark:text-green-400 mt-2">Retrait disponible !</p>
            @else
                <p class="text-xs text-sky-600 dark:text-sky-400 mt-2">
                    Encore $ {{ number_format($remaining, 2) }}
                    ({{ number_format($remaining * \App\Models\ExchangeRate::current(), 0, ',', '.') }} FC)
                </p>
            @endif
        </div>
    </div>
</div>
