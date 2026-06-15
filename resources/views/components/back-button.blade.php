@props(['href' => null, 'label' => 'Retour'])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 sm:gap-2 text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition whitespace-nowrap']) }} title="{{ $label }}">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="hidden sm:inline">{{ $label }}</span>
    </a>
@else
    <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ route('dashboard') }}'" {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 sm:gap-2 text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition whitespace-nowrap']) }} title="{{ $label }}">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="hidden sm:inline">{{ $label }}</span>
    </button>
@endif
