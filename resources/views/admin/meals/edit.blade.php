<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Modifier repas</h1>
    </div>

    <div class="max-w-xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.meals.update', $meal) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom</label>
                <input type="text" name="name" required value="{{ old('name', $meal->name) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
            </div>
            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <button type="button" id="btn-generate-desc" class="inline-flex items-center gap-1.5 rounded-lg bg-purple-600 px-3 py-1 text-xs font-semibold text-white transition hover:bg-purple-700 disabled:opacity-50">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span id="btn-generate-label">Générer avec l'IA</span>
                    </button>
                </div>
                <textarea name="description" id="description" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">{{ old('description', $meal->description) }}</textarea>
                <p id="desc-error" class="mt-1 hidden text-xs text-red-600 dark:text-red-400"></p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Devise</label>
                    <select name="currency" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                        <option value="usd" {{ old('currency', 'usd') === 'usd' ? 'selected' : '' }}>USD ($)</option>
                        <option value="fc" {{ old('currency') === 'fc' ? 'selected' : '' }}>Francs congolais (FC)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix</label>
                    <input type="number" step="0.01" name="price" required value="{{ old('price', $meal->price) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Prix (FC)</label>
                    <input type="number" step="1" name="price_fc" value="{{ old('price_fc', $meal->price_fc) }}" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Laissez vide pour calculer automatiquement (taux 2800)</p>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catégorie</label>
                <select name="category_id" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="">-- Choisir --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $meal->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                <select name="status" required class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500">
                    <option value="available" {{ old('status', $meal->status) === 'available' ? 'selected' : '' }}>Disponible</option>
                    <option value="unavailable" {{ old('status', $meal->status) === 'unavailable' ? 'selected' : '' }}>Indisponible</option>
                </select>
            </div>
            @if($meal->image)
                <div class="mb-4">
                    <img src="{{ str_starts_with($meal->image, 'http') ? $meal->image : asset('storage/' . $meal->image) }}" class="h-32 rounded-lg object-cover">
                </div>
            @endif
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Image (laisser vide pour conserver)</label>
                <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            </div>
            <div class="mb-6 flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ $meal->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Actif</label>
            </div>
            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition">Enregistrer</button>
        </form>
    </div>

    <script>
        document.getElementById('btn-generate-desc')?.addEventListener('click', async function () {
            const btn = this;
            const label = document.getElementById('btn-generate-label');
            const nameInput = document.querySelector('input[name="name"]');
            const categorySelect = document.querySelector('select[name="category_id"]');
            const descField = document.getElementById('description');
            const errorEl = document.getElementById('desc-error');

            errorEl.classList.add('hidden');

            if (!nameInput.value.trim()) {
                errorEl.textContent = 'Veuillez d\'abord saisir le nom du plat.';
                errorEl.classList.remove('hidden');
                return;
            }

            btn.disabled = true;
            label.textContent = 'Génération...';

            try {
                const res = await fetch('{{ route("admin.meals.generate-description") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        name: nameInput.value.trim(),
                        category_id: categorySelect?.value || null,
                    }),
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.error || 'Erreur lors de la génération.');
                }

                descField.value = data.description;
            } catch (e) {
                errorEl.textContent = e.message;
                errorEl.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                label.textContent = 'Générer avec l\'IA';
            }
        });
    </script>
</x-app-layout>
