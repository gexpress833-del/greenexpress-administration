<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Logo et titre --}}
    <div class="text-center mb-6">
        <img src="/logo.png" alt="Green Express" class="mx-auto h-20 w-auto mb-4 object-contain">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-gray-100">Green Express</h1>
        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-1">Récupération de compte</p>
    </div>

    <div class="mb-6 text-sm text-gray-600 dark:text-gray-400">
        Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Adresse email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                    </svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition"
                       placeholder="votre@email.com">
            </div>
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Bouton envoi --}}
        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2 text-sm sm:text-base">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            ENVOYER LE LIEN
        </button>

        {{-- Retour --}}
        <div class="text-center">
            <a href="{{ route('login') }}" class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 font-medium">
                ← Retour à la connexion
            </a>
        </div>
    </form>
</x-guest-layout>
