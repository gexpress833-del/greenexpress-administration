@extends('layouts.app')

@section('title', 'À propos — Green Express')

@section('content')
<style>
@keyframes fade-up {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-up {
    animation: fade-up 0.8s ease-out forwards;
}
.animate-delay-1 { animation-delay: 0.1s; opacity: 0; }
.animate-delay-2 { animation-delay: 0.25s; opacity: 0; }
.animate-delay-3 { animation-delay: 0.4s; opacity: 0; }
.animate-delay-4 { animation-delay: 0.55s; opacity: 0; }
.animate-delay-5 { animation-delay: 0.7s; opacity: 0; }
</style>

<div class="max-w-3xl mx-auto px-4 py-8 lg:py-12">

    {{-- En-tête --}}
    <div class="text-center mb-12 animate-fade-up">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-800 shadow-xl mb-6">
            <img src="/logo.png" alt="Green Express" class="h-14 w-auto drop-shadow-md">
        </div>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight mb-3">Green Express</h1>
        <p class="text-base text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
            Une infrastructure alimentaire conçue pour simplifier l'accès au bien-manger, un repas à la fois.
        </p>
    </div>

    {{-- Mission --}}
    <section class="mb-10 animate-fade-up animate-delay-1">
        <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-4">Notre mission</h2>
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 lg:p-8 shadow-sm border border-slate-200 dark:border-slate-800">
            <p class="text-slate-700 dark:text-slate-300 leading-relaxed text-[15px]">
                Green Express articule une réponse structurée à un défi quotidien : permettre à chacun de bénéficier d'une alimentation équilibrée, préparée avec rigueur et livrée avec ponctualité. Nous ne sommes pas un simple service de livraison — nous sommes une chaîne de valeur pensée pour réconcilier efficience opérationnelle et exigence nutritionnelle.
            </p>
        </div>
    </section>

    {{-- Ce que nous proposons --}}
    <section class="mb-10 animate-fade-up animate-delay-2">
        <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-4">Nos services</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Restauration soignée</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Des menus élaborés par des professionnels de la gastronomie, pensés pour l'équilibre nutritionnel sans sacrifier le plaisir du goût.</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0z"/></svg>
                </div>
                <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Logistique et livraisons validées</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Un réseau de livreurs structuré et géolocalisé. Chaque repas livré est confirmé sur la plateforme, ce qui déclenche automatiquement la récompense en points du livreur.</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Abonnements avec menu hebdomadaire</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Chaque abonnement définit un menu précis du lundi au vendredi. Les commandes et livraisons journalières sont générées automatiquement après validation par l'administrateur.</p>
            </div>
            <div class="bg-white dark:bg-slate-900 rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Transactions sécurisées</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Un système de paiement intégré avec conversion automatique USD/FC, assurant transparence et fiabilité à chaque transaction.</p>
            </div>
        </div>
    </section>

    {{-- Notre histoire --}}
    <section class="mb-10 animate-fade-up animate-delay-3">
        <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-4">Notre histoire</h2>
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 lg:p-8 shadow-sm border border-slate-200 dark:border-slate-800">
            <p class="text-slate-700 dark:text-slate-300 leading-relaxed text-[15px] mb-4">
                Green Express est le fruit d'une conviction : l'alimentation de qualité ne devrait pas être un privilège géographique ou économique. Partant de ce constat, nous avons construit une plateforme qui connecte des chefs talentueux, des livreurs engagés et des clients exigeants dans un écosystème vertueux.
            </p>
            <p class="text-slate-700 dark:text-slate-300 leading-relaxed text-[15px]">
                Au fil des itérations, notre modèle s'est affiné. De la simple livraison de repas, nous sommes passés à une infrastructure complète de gestion alimentaire : planification hebdomadaire des menus, abonnements avec génération automatique des commandes journalières, suivi des livraisons, système de change en temps réel et programme de points pour agents et livreurs. Chaque fonctionnalité reflète une attention méticuleuse portée aux besoins réels de nos utilisateurs.
            </p>
        </div>
    </section>

    {{-- Valeurs --}}
    <section class="mb-10 animate-fade-up animate-delay-4">
        <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-4">Nos principes directeurs</h2>
        <div class="space-y-3">
            <div class="flex items-start gap-4 bg-white dark:bg-slate-900 rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="w-2 h-2 mt-2 rounded-full bg-emerald-500 shrink-0"></div>
                <div>
                    <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Excellence opérationnelle</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Chaque processus est pensé pour minimiser la friction et maximiser la valeur perçue par l'utilisateur final.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 bg-white dark:bg-slate-900 rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="w-2 h-2 mt-2 rounded-full bg-emerald-500 shrink-0"></div>
                <div>
                    <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Transparence économique</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Taux de change affichés en temps réel, tarification claire — aucune opacité n'a sa place ici.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 bg-white dark:bg-slate-900 rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="w-2 h-2 mt-2 rounded-full bg-emerald-500 shrink-0"></div>
                <div>
                    <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Développement communautaire</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Nous créons des opportunités de revenus pour nos agents et livreurs. Un programme de points récompense les abonnements validés et les livraisons confirmées, tandis que l'administrateur peut appliquer des ajustements en cas de problème.</p>
                </div>
            </div>
            <div class="flex items-start gap-4 bg-white dark:bg-slate-900 rounded-xl p-5 shadow-sm border border-slate-200 dark:border-slate-800">
                <div class="w-2 h-2 mt-2 rounded-full bg-emerald-500 shrink-0"></div>
                <div>
                    <h3 class="font-semibold text-slate-900 dark:text-white mb-1">Innovation pragmatique</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Chaque évolution technologique répond à un besoin concret. Nous privilégions l'utilité à la sophistication ostentatoire.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Rejoindre --}}
    <section class="mb-10 animate-fade-up animate-delay-5">
        <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest mb-4">Rejoindre l'écosystème</h2>
        <div class="bg-gradient-to-br from-emerald-800 to-emerald-950 rounded-2xl p-6 lg:p-8 text-white shadow-xl">
            <p class="leading-relaxed text-emerald-100/90 text-[15px] mb-5">
                Que vous souhaitiez commander, distribuer ou contribuer à la croissance de Green Express, notre plateforme vous offre les outils et la structure nécessaires pour vous épanouir dans un environnement digne de confiance.
            </p>
            <div class="flex flex-wrap gap-3">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 backdrop-blur border border-white/10 text-sm">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Client
                </span>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 backdrop-blur border border-white/10 text-sm">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Agent
                </span>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 backdrop-blur border border-white/10 text-sm">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0 2 2 0 00-4 0z"/></svg>
                    Livreur
                </span>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 backdrop-blur border border-white/10 text-sm">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                    Cuisinier
                </span>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <div class="text-center text-xs text-slate-400 dark:text-slate-500 pt-4 border-t border-slate-200 dark:border-slate-800">
        <p>Green Express &copy; {{ date('Y') }}. Tous droits réservés.</p>
    </div>

</div>
@endsection
