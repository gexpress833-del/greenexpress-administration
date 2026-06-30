@extends('layouts.app')

@section('title', 'Nos Repas — Green Express')

@section('content')
<style>
@keyframes meal-fade-up { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }
@keyframes meal-shimmer { 0% { background-position: 0% center; } 100% { background-position: 220% center; } }
.meal-reveal { animation: meal-fade-up .55s ease-out both; }
.meal-card { transition: transform .3s ease, border-color .3s ease, box-shadow .3s ease; }
.meal-card:hover { transform: translateY(-6px); box-shadow: 0 24px 60px -24px rgba(0,0,0,.55); }
.meal-image { transition: transform .55s ease; }
.meal-card:hover .meal-image { transform: scale(1.07); }
.meal-shimmer { background: linear-gradient(90deg, #22c55e, #facc15, #16a34a, #22c55e); background-size: 220% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: meal-shimmer 4s linear infinite; }
</style>

<div class="-m-4 -mt-[calc(1rem+4rem+env(safe-area-inset-top))] lg:-m-8 lg:-mt-[calc(2rem+4rem+env(safe-area-inset-top))] min-h-screen overflow-hidden bg-slate-950 text-white">
    <div class="relative min-h-screen">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(34,197,94,0.25),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(250,204,21,0.16),transparent_34%)]"></div>
        <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgba(255,255,255,.12) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.12) 1px, transparent 1px); background-size: 42px 42px;"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8" style="padding-top: calc(1.25rem + 4rem + env(safe-area-inset-top));">
            <section class="meal-reveal mb-8 overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] p-6 shadow-2xl shadow-black/30 backdrop-blur-2xl sm:p-8 lg:p-10">
                <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-end">
                    <div>
                        <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-emerald-200">Menu Green Express</div>
                        <h1 class="meal-shimmer text-4xl font-black tracking-tight sm:text-5xl lg:text-6xl">Nos Repas</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-relaxed text-slate-300 sm:text-base">Découvrez une sélection de plats préparés avec soin par nos chefs partenaires.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-slate-950/45 px-5 py-4 text-sm text-slate-300">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Disponible</p>
                        <p class="mt-1 text-2xl font-black text-white">{{ $meals->count() }} plat{{ $meals->count() > 1 ? 's' : '' }}</p>
                    </div>
                </div>
            </section>

            <section class="meal-reveal mb-8 rounded-[2rem] border border-white/10 bg-white/[0.07] p-4 shadow-2xl shadow-black/20 backdrop-blur-2xl sm:p-5" style="animation-delay:.08s;">
                <div class="grid gap-4 lg:grid-cols-[320px_1fr] lg:items-center">
                    <form method="GET" action="{{ route('meals.public') }}" class="w-full">
                        <div class="relative">
                            <svg class="absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un plat..." class="w-full rounded-2xl border-white/10 bg-slate-950/60 py-3 pl-12 pr-4 text-sm text-white placeholder-slate-500 focus:border-emerald-400 focus:ring-emerald-400">
                        </div>
                    </form>
                    <div class="flex gap-2 overflow-x-auto pb-1">
                        <a href="{{ route('meals.public') }}" class="shrink-0 rounded-2xl px-4 py-3 text-sm font-bold transition {{ request('category') ? 'border border-white/10 bg-white/10 text-slate-300 hover:bg-white/15' : 'bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-400/20' }}">Tous</a>
                        @foreach($categories as $category)
                            <a href="{{ route('meals.public', ['category' => $category->id]) }}" class="shrink-0 rounded-2xl px-4 py-3 text-sm font-bold transition {{ request('category') == $category->id ? 'bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-400/20' : 'border border-white/10 bg-white/10 text-slate-300 hover:bg-white/15' }}">{{ $category->name }}</a>
                        @endforeach
                    </div>
                </div>
            </section>

            @if($meals->isEmpty())
                <div class="meal-reveal rounded-[2rem] border border-dashed border-white/15 bg-white/[0.05] px-6 py-16 text-center text-slate-400">Aucun repas disponible pour le moment.</div>
            @else
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach($meals as $i => $meal)
                        <article class="meal-card meal-reveal overflow-hidden rounded-[2rem] border border-white/10 bg-white/[0.07] shadow-2xl shadow-black/20 backdrop-blur-2xl" style="animation-delay: {{ 0.12 + ($i * 0.04) }}s;">
                            <div class="relative h-56 overflow-hidden bg-slate-900">
                                @if($meal->image)
                                    <img src="{{ str_starts_with($meal->image, 'http') ? $meal->image : asset('storage/' . $meal->image) }}" alt="{{ $meal->name }}" class="meal-image h-full w-full object-cover">
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-slate-600"><svg class="h-14 w-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/10 to-transparent"></div>
                                @if($meal->category)
                                    <span class="absolute left-4 top-4 rounded-full border border-white/20 bg-slate-950/55 px-3 py-1 text-[10px] font-black uppercase tracking-[0.18em] text-white backdrop-blur">{{ $meal->category->name }}</span>
                                @endif
                                <span class="absolute bottom-4 right-4 rounded-full bg-emerald-400 px-3 py-1 text-xs font-black text-slate-950">Disponible</span>
                            </div>
                            <div class="p-5">
                                <h3 class="text-lg font-black text-white">{{ $meal->name }}</h3>
                                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-slate-400">{{ $meal->description ?? 'Un plat savoureux préparé avec des ingrédients frais et de qualité.' }}</p>
                                <div class="mt-5 flex items-end justify-between border-t border-white/10 pt-4">
                                    <div>
                                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Prix</p>
                                        <p class="mt-1 text-2xl font-black text-emerald-300">${{ number_format($meal->price, 2) }}</p>
                                    </div>
                                    <p class="text-sm font-bold text-yellow-300">{{ number_format($meal->price_fc, 0, ',', '.') }} FC</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
