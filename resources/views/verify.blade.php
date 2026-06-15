@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Vérifier un document Green Express</h1>

    <form method="get" action="{{ route('verify.show') }}" class="mb-6">
        <label class="block mb-2 font-medium">Code du reçu / commande</label>
        <div class="flex gap-2">
            <input name="code" value="{{ old('code', $code ?? '') }}" class="flex-1 border rounded px-3 py-2" placeholder="Entrez le code de commande" />
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Vérifier</button>
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
