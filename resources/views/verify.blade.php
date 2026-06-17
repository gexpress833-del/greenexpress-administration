@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Vérifier un document Green Express</h1>

    <form method="get" action="{{ route('verify.show') }}" class="mb-6" x-data="{ loading: false }" @submit="loading = true">
        <label class="block mb-2 font-medium">Code du reçu / commande</label>
        <div class="flex gap-2">
            <input name="code" value="{{ old('code', $code ?? '') }}" class="flex-1 border rounded px-3 py-2 read-only:opacity-60 read-only:cursor-not-allowed" placeholder="Entrez le code de commande" :readonly="loading" />
            <button type="submit" :disabled="loading" class="bg-green-600 hover:bg-green-700 disabled:bg-green-500 text-white px-4 py-2 rounded transition flex items-center gap-2 disabled:cursor-not-allowed">
                <template x-if="loading">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                    </svg>
                </template>
                <span x-text="loading ? 'Vérification...' : 'Vérifier'">Vérifier</span>
            </button>
        </div>
    </form>

    @if(isset($code) && !$order)
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded">Aucun document trouvé pour le code "{{ $code }}".</div>
    @elseif($order)
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded">
            <h2 class="text-lg font-semibold">Document authentique</h2>
            <p class="mt-2">Document authentique — commande <strong>#{{ $order->code }}</strong></p>
            <p class="text-sm text-gray-700">Date de passation : <strong>{{ $order->created_at->toDayDateTimeString() }}</strong></p>
        </div>
    @endif
</div>
@endsection
