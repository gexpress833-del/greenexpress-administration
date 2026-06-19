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
    cursor: pointer;
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
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

<div x-data="mealCatalog()" class="max-w-6xl mx-auto px-4 py-6 lg:py-10" @keydown.escape.window="detailOpen = false">

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
                     style="animation-delay: {{ 0.15 + ($i * 0.05) }}s;"
                     @click="openMeal({{ json_encode([
                         'id' => $meal->id,
                         'name' => $meal->name,
                         'description' => $meal->description,
                         'price' => (float) $meal->price,
                         'price_fc' => (float) $meal->price_fc,
                         'image' => $meal->image ? (str_starts_with($meal->image, 'http') ? $meal->image : asset('storage/' . $meal->image)) : null,
                         'category' => optional($meal->category)->name,
                     ]) }})">
                    {{-- Image --}}
                    <div class="relative h-48 overflow-hidden bg-slate-100 dark:bg-slate-800">
                        @if($meal->image)
                            <img src="{{ str_starts_with($meal->image, 'http') ? $meal->image : asset('storage/' . $meal->image) }}" alt="{{ $meal->name }}" class="meal-image w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-12 h-12 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        @if($meal->category)
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-black/40 backdrop-blur text-white text-[10px] font-semibold uppercase tracking-wider">
                                {{ $meal->category->name }}
                            </span>
                        @endif
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-slate-900 dark:text-white text-base mb-1 truncate">{{ $meal->name }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-4 line-clamp-2">{{ $meal->description ?? 'Un plat savoureux préparé avec des ingrédients frais et de qualité.' }}</p>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                            <div>
                                <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">${{ number_format($meal->price, 2) }}</span>
                                <span class="text-xs text-slate-400 dark:text-slate-500 ml-1">/ {{ number_format($meal->price_fc, 0, ',', '.') }} FC</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                </span>
                                <span class="text-[10px] font-medium text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-lg">Disponible</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($meals->isNotEmpty())
        <div class="text-center mt-10 text-xs text-slate-400 dark:text-slate-500 animate-fade-up" style="animation-delay: 0.8s;">
            {{ $meals->count() }} plat{{ $meals->count() > 1 ? 's' : '' }} disponible{{ $meals->count() > 1 ? 's' : '' }} — Green Express
        </div>
    @endif

    {{-- ==================== MODAL DETAIL ==================== --}}
    <div x-show="detailOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4" style="display:none">
        <div x-show="detailOpen"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="detailOpen = false" class="absolute inset-0 bg-black/70 backdrop-blur-md"></div>

        <div x-show="detailOpen"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90 translate-y-8" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-90 translate-y-8"
             class="relative bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-3xl overflow-hidden max-h-[92vh] flex flex-col">

            {{-- Bouton fermeture --}}
            <button @click="detailOpen = false" class="absolute top-4 right-4 z-20 w-10 h-10 rounded-full bg-white/90 dark:bg-slate-800/90 backdrop-blur text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-white hover:dark:bg-slate-700 hover:text-slate-900 hover:dark:text-white transition shadow-lg active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="flex flex-col md:flex-row overflow-y-auto">
                {{-- Image avec overlay dégradé --}}
                <div class="relative md:w-1/2 h-64 sm:h-72 md:h-auto min-h-[280px] bg-slate-100 dark:bg-slate-800 shrink-0 overflow-hidden">
                    <template x-if="selectedMeal?.image">
                        <img :src="selectedMeal.image" :alt="selectedMeal.name" class="w-full h-full object-cover">
                    </template>
                    <template x-if="!selectedMeal?.image">
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-20 h-20 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </template>
                    {{-- Overlay dégradé bas --}}
                    <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/40 to-transparent pointer-events-none"></div>
                    {{-- Badge catégorie --}}
                    <div class="absolute top-4 left-4">
                        <span x-show="selectedMeal?.category" x-text="selectedMeal?.category"
                              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <span x-text="selectedMeal?.category"></span>
                        </span>
                    </div>
                </div>

                {{-- Informations --}}
                <div class="md:w-1/2 p-6 sm:p-8 flex flex-col">
                    {{-- Nom --}}
                    <h2 x-text="selectedMeal?.name" class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white mb-3 leading-tight"></h2>

                    {{-- Séparateur --}}
                    <div class="w-12 h-1 rounded-full bg-emerald-500 mb-4"></div>

                    {{-- Description --}}
                    <p x-text="selectedMeal?.description ?? 'Un plat savoureux préparé avec des ingrédients frais et de qualité.'"
                       class="text-sm sm:text-base text-slate-500 dark:text-slate-400 leading-relaxed mb-6 flex-1"></p>

                    {{-- Section prix --}}
                    <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-5 mb-5 border border-slate-100 dark:border-slate-700">
                        <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Prix</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">$<span x-text="selectedMeal?.price?.toFixed(2)"></span></span>
                            <span class="text-base text-slate-400 dark:text-slate-500">/ <span x-text="Math.round(selectedMeal?.price_fc || 0).toLocaleString('fr-FR')"></span> FC</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    @auth
                        @if(auth()->user()->isAgent())
                            <a href="{{ route('agent.orders.create') }}" class="group flex items-center justify-center gap-2 w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold py-3.5 px-6 rounded-xl transition-all shadow-lg shadow-emerald-600/20 hover:shadow-emerald-600/30 active:scale-[0.98] text-sm">
                                <svg class="w-5 h-5 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                                Commander ce plat
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function mealCatalog() {
    return {
        detailOpen: false,
        selectedMeal: null,
        openMeal(meal) {
            this.selectedMeal = meal;
            this.detailOpen = true;
        }
    }
}
</script>
@endpush
@endsection
