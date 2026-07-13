<x-app-layout>
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Mon profil</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez vos informations personnelles et la sécurité de votre compte</p>
        </div>
        <x-back-button :href="route('dashboard')" />
    </div>

    @if (session('error') === 'avatar-upload-failed')
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
             class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span><strong>Erreur :</strong> L'upload de la photo a échoué. Vérifiez que Cloudinary est configuré.</span>
            <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    @endif

    {{-- Hero premium --}}
    <div class="relative rounded-3xl shadow-2xl overflow-hidden mb-8 text-white bg-gradient-to-br from-emerald-500 via-green-600 to-teal-700">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'80\' height=\'80\' viewBox=\'0 0 80 80\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.06\'%3E%3Cpath d=\'M40 40c0-11.046 8.954-20 20-20s20 8.954 20 20-8.954 20-20 20-20-8.954-20-20zm0 0c0 11.046-8.954 20-20 20s-20-8.954-20-20 8.954-20 20-20 20 8.954 20 20z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-emerald-300/20 rounded-full blur-3xl"></div>
        <div class="relative px-8 py-10">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                <div class="relative group shrink-0">
                    <div class="w-28 h-28 rounded-3xl ring-4 ring-white/40 shadow-2xl overflow-hidden bg-white/20 backdrop-blur-md">
                        @if ($user->avatar)
                            <img src="{{ $user->avatar }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-5xl font-bold text-white/90">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-9 h-9 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                </div>
                <div class="text-center md:text-left flex-1">
                    <h2 class="text-3xl font-bold tracking-tight">{{ $user->name }}</h2>
                    <p class="text-green-50 text-sm mt-2 font-medium">{{ $user->email }}</p>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mt-4">
                        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm border border-white/20">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ ucfirst($user->role) }}
                        </span>
                        @if($user->is_active)
                            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-semibold bg-emerald-400/30 backdrop-blur-sm border border-emerald-400/30">
                                <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                                Actif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-semibold bg-red-400/30 backdrop-blur-sm border border-red-400/30">
                                Inactif
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-semibold bg-white/10 backdrop-blur-sm border border-white/10">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Membre depuis {{ $user->created_at->format('M Y') }}
                        </span>
                    </div>
                </div>
                <div class="hidden md:block text-right shrink-0">
                    <div class="text-4xl font-bold tracking-tight">#{{ $user->id }}</div>
                    <div class="text-xs text-green-100 uppercase tracking-widest mt-1">ID Utilisateur</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        {{-- Colonne principale --}}
        <div class="xl:col-span-2 space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50/80 to-white dark:from-gray-700/30 dark:to-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </span>
                        Informations personnelles
                    </h3>
                </div>
                <div class="p-8">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50/80 to-white dark:from-gray-700/30 dark:to-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        Sécurité du compte
                    </h3>
                </div>
                <div class="p-8">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

        </div>

        {{-- Colonne secondaire --}}
        <div class="space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-8 py-5 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-gray-50/80 to-white dark:from-gray-700/30 dark:to-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Aperçu du compte</h3>
                </div>
                <div class="p-6 space-y-3">
                    @php
                        $accountStats = [
                            ['label' => 'ID Utilisateur', 'value' => '#'.$user->id, 'icon' => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0v5a2 2 0 01-2 2H5m14-8h-5m4 0v5a2 2 0 01-2 2h-5', 'color' => 'blue'],
                            ['label' => 'Rôle', 'value' => ucfirst($user->role), 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'purple'],
                            ['label' => 'Statut', 'value' => $user->is_active ? 'Compte actif' : 'Compte inactif', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => $user->is_active ? 'emerald' : 'red'],
                            ['label' => 'Inscrit le', 'value' => $user->created_at->format('d/m/Y'), 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'amber'],
                            ['label' => 'Dernière mise à jour', 'value' => $user->updated_at->format('d/m/Y H:i'), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'cyan'],
                        ];
                    @endphp

                    @foreach($accountStats as $stat)
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-gray-50/70 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <div class="w-11 h-11 rounded-xl bg-{{ $stat['color'] }}-100 dark:bg-{{ $stat['color'] }}-900/30 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $stat['label'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $stat['value'] }}</div>
                            </div>
                        </div>
                    @endforeach

                    @if($user->email_verified_at)
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50">
                            <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-medium text-emerald-900 dark:text-emerald-300">Email vérifié</div>
                                <div class="text-xs text-emerald-600 dark:text-emerald-400">{{ $user->email_verified_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-3xl shadow-xl p-6 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -mr-10 -mt-10"></div>
                <h3 class="text-lg font-semibold mb-4 relative">Actions rapides</h3>
                <div class="space-y-3 relative">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 p-3.5 rounded-xl bg-white/10 hover:bg-white/20 transition backdrop-blur-sm border border-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span class="text-sm font-medium">Tableau de bord</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3.5 rounded-xl bg-white/10 hover:bg-white/20 transition backdrop-blur-sm border border-white/10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span class="text-sm font-medium">Actualiser</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
