@props([
    'routeName',
    'nameSelector' => 'input[name="name"]',
    'descSelector' => '#description',
    'label' => "Générer avec l'IA",
    'extraData' => [],
])

<div class="flex items-center justify-between">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
    <button type="button"
        data-ai-route="{{ route($routeName) }}"
        data-ai-name-selector="{{ $nameSelector }}"
        data-ai-desc-selector="{{ $descSelector }}"
        data-ai-extra='@json($extraData)'
        class="ai-generate-btn inline-flex items-center gap-1.5 rounded-lg bg-purple-600 px-3 py-1 text-xs font-semibold text-white transition hover:bg-purple-700 disabled:opacity-50">
        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <span class="ai-generate-label">Générer avec l'IA</span>
    </button>
</div>
<p class="ai-error mt-1 hidden text-xs text-red-600 dark:text-red-400"></p>

<script>
document.querySelector('.ai-generate-btn')?.addEventListener('click', async function () {
    const btn = this;
    const label = btn.querySelector('.ai-generate-label');
    const nameInput = document.querySelector(btn.dataset.aiNameSelector);
    const descField = document.querySelector(btn.dataset.aiDescSelector);
    const errorEl = btn.closest('div').parentElement.querySelector('.ai-error');
    const extraData = JSON.parse(btn.dataset.aiExtra || '{}');

    errorEl?.classList.add('hidden');

    if (!nameInput?.value.trim()) {
        if (errorEl) {
            errorEl.textContent = "Veuillez d'abord saisir le nom.";
            errorEl.classList.remove('hidden');
        }
        return;
    }

    btn.disabled = true;
    label.textContent = 'Génération...';

    try {
        const body = { name: nameInput.value.trim(), ...extraData };
        const extraKeys = Object.keys(extraData);
        extraKeys.forEach(key => {
            const el = document.querySelector(`[name="${key}"]`);
            if (el) body[key] = el.value || null;
        });

        const res = await fetch(btn.dataset.aiRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        });

        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.error || 'Erreur lors de la génération.');
        }

        if (descField) descField.value = data.description;
    } catch (e) {
        if (errorEl) {
            errorEl.textContent = e.message;
            errorEl.classList.remove('hidden');
        }
    } finally {
        btn.disabled = false;
        label.textContent = "Générer avec l'IA";
    }
});
</script>
