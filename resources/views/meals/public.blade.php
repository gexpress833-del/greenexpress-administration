@extends('layouts.app')

@section('title', 'Nos Repas — Green Express')

@section('content')
<style>
@keyframes fade-up {
    from { opacity: 0; transform: translateY(24px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-up {
    animation: fade-up 0.6s ease-out forwards;
}
.meal-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.meal-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
}
.meal-image {
    transition: transform 0.5s ease;
}
.meal-card:hover .meal-image {
    transform: scale(1.05);
}
</style>

<div class="max-w-6xl mx-auto px-4 py-6 lg:py-10">

    {{-- En-tête --}}
    <div class="text-center mb-8 animate-fade-up">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-700 shadow-lg mb-4">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight mb-2">Nos Repas</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">Découvrez une sélection de plats préparés avec soin par nos chefs partenaires.</p>
    </div>

    {{-- Filtres --}}
    <div class="flex flex-col sm:flex-row items-center gap-3 mb-8 animate-fade-up" style="animation-delay: 0.1s;">
        <form method="GET" action="{{ route('meals.public') }}" class="flex-1 w-full sm:max-w-xs">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un plat..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition">
            </div>
        </form>

        <div class="flex gap-2 overflow-x-auto pb-1 w-full sm:w-auto no-scrollbar">
            <a href="{{ route('meals.public') }}"
               class="shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition {{ request('category') ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200' : 'bg-emerald-600 text-white shadow-md' }}">
                Tous
            </a>
            @foreach($categories as $category)
                <a href="{{ route('meals.public', ['category' => $category->id]) }}"
                   class="shrink-0 px-4 py-2 rounded-xl text-sm font-medium transition {{ request('category') == $category->id ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Grille de repas --}}
    @if($meals->isEmpty())
        <div class="text-center py-16 animate-fade-up">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Aucun repas disponible pour le moment.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($meals as $i => $meal)
                <div class="meal-card bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm animate-fade-up"
                     style="animation-delay: {{ 0.15 + ($i * 0.05) }}s;">
                    {{-- Image --}}
                    <div class="relative h-48 overflow-hidden bg-slate-100 dark:bg-slate-800">
                        @if($meal->image)
                            <img src="{{ $meal->image }}" alt="{{ $meal->name }}" class="meal-image w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        {{-- Badge catégorie --}}
                        @if($meal->category)
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-black/40 backdrop-blur text-white text-[10px] font-semibold uppercase tracking-wider">
                                {{ $meal->category->name }}
                            </span>
                        @endif
                        {{-- Badge disponible --}}
                        <span class="absolute top-3 right-3 w-2.5 h-2.5 rounded-full bg-emerald-400 shadow-lg shadow-emerald-400/50"></span>
                    </div>

                    {{-- Contenu --}}
                    <div class="p-5">
                        <h3 class="font-bold text-slate-900 dark:text-white text-base mb-1 truncate">{{ $meal->name }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4 line-clamp-2">{{ $meal->description ?? 'Un plat savoureux préparé avec des ingrédients frais et de qualité.' }}</p>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($meal->price, 2) }}</span>
                                <span class="text-xs text-slate-400 dark:text-slate-500 ml-1">/ {{ number_format($meal->price_fc, 0, ',', '.') }} FC</span>
                            </div>
                            <span class="text-[10px] font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-lg">
                                Disponible
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Pied de page --}}
    @if($meals->isNotEmpty())
        <div class="text-center mt-10 text-xs text-slate-400 dark:text-slate-500 animate-fade-up" style="animation-delay: 0.8s;">
            {{ $meals->count() }} plat{{ $meals->count() > 1 ? 's' : '' }} disponible{{ $meals->count() > 1 ? 's' : '' }} — Green Express
        </div>
    @endif

</div>
@endsection
