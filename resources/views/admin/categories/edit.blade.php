<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Modifier catégorie</h1>
        <x-back-button :href="route('admin.categories.index')" />
    </div>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.categories.update', $category) }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom</label>
                <input type="text" name="name" required value="{{ old('name', $category->name) }}" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                <textarea name="description" rows="2" :disabled="loading" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="mb-6 flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }} :disabled="loading" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500 disabled:opacity-60 disabled:cursor-not-allowed">
                <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Actif</label>
            </div>
            <button type="submit" :disabled="loading" class="w-full bg-green-600 hover:bg-green-700 disabled:bg-green-500 text-white font-semibold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2 disabled:cursor-not-allowed">
                <template x-if="loading">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                </template>
                <span x-text="loading ? 'Enregistrement...' : 'Enregistrer'">Enregistrer</span>
            </button>
        </form>
    </div>
</x-app-layout>
