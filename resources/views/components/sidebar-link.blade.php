@props(['href', 'active' => false, 'icon'])

@php
$classes = $active
    ? 'flex items-center gap-3 pl-4 pr-5 py-3.5 rounded-lg mb-1 transition border-l-4 bg-white/[0.12] border-green-500 font-semibold'
    : 'flex items-center gap-3 pl-4 pr-5 py-3.5 rounded-lg mb-1 transition border-l-4 border-transparent hover:bg-green-800';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        {!! $icon !!}
    @endif
    <span>{{ $slot }}</span>
</a>
