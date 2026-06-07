<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Mon profil</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gérez vos informations personnelles et votre sécurité</p>
        </div>
        <x-back-button :href="route('dashboard')" />
    </div>

    {{-- En-tête profil utilisateur --}}
    <div class="bg-gradient-to-r from-green-600 to-green-700 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
            <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center text-3xl font-bold shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="text-center sm:text-left">
                <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                <p class="text-green-100 text-sm">{{ $user->email }}</p>
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white uppercase">
                        {{ $user->role }}
                    </span>
                    @if($user->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-400/30 text-green-100">
                            Actif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-400/30 text-red-100">
                            Inactif
                        </span>
                    @endif
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white/20 text-white">
                        Membre depuis {{ $user->created_at->format('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Colonne secondaire --}}
        <div class="space-y-6">
            {{-- Informations du compte --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Informations du compte</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">ID</span>
                        <span class="font-mono text-gray-800 dark:text-gray-100">#{{ $user->id }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">Rôle</span>
                        <span class="font-medium text-gray-800 dark:text-gray-100 uppercase">{{ $user->role }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">Statut</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                            {{ $user->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">Inscrit le</span>
                        <span class="text-gray-800 dark:text-gray-100">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">Dernière modif.</span>
                        <span class="text-gray-800 dark:text-gray-100">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($user->email_verified_at)
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500 dark:text-gray-400">Email vérifié</span>
                            <span class="text-green-600 dark:text-green-400 text-xs">Oui</span>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
