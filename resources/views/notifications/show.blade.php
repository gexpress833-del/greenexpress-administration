<x-app-layout>
    <div class="mx-auto max-w-3xl">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('notifications.history') }}" class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-200" aria-label="Retour">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Détail de la notification</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Consultez le contenu complet de votre notification.</p>
            </div>
        </div>

        <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-start gap-4 border-b border-gray-100 p-6 dark:border-gray-700">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl text-2xl {{ $typeColor }}">
                    {{ $icon }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="mb-2 flex flex-wrap items-center gap-2">
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $typeColor }}">{{ $category }}</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $createdAt->format('d/m/Y à H:i') }}</span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $title }}</h2>
                </div>
            </div>

            <div class="p-6">
                <p class="whitespace-pre-line text-sm leading-7 text-gray-600 dark:text-gray-300">{{ $message }}</p>

                @if($url)
                    <div class="mt-6">
                        <a href="{{ $url }}" class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-700">
                            Ouvrir la page concernée
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </article>
    </div>
</x-app-layout>
