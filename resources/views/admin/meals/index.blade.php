<x-app-layout>
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Repas</h1>
        <a href="{{ route('admin.meals.create') }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition">
            + Ajouter
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($meals as $meal)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                @if($meal->image)
                    <img src="{{ str_starts_with($meal->image, 'http') ? $meal->image : asset('storage/' . $meal->image) }}" alt="{{ $meal->name }}" class="w-full h-40 object-cover">
                @else
                    <div class="w-full h-40 bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 dark:text-gray-500 text-sm">Pas d'image</div>
                @endif
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100">{{ $meal->name }}</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $meal->status === 'available' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' }}">
                            {{ $meal->status }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ Str::limit($meal->description, 80) }}</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-lg font-bold text-green-700 dark:text-green-400">$ {{ number_format($meal->price, 2) }}</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">{{ number_format($meal->price_fc, 0, ',', '.') }} FC</span>
                        </div>
                        <a href="{{ route('admin.meals.edit', $meal) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">Modifier</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-10 text-gray-500 dark:text-gray-400">Aucun repas</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $meals->links() }}
    </div>
</x-app-layout>
