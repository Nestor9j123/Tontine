{{-- Reçu temporaire à montrer au client AVANT validation --}}
<div class="bg-white p-6 rounded-lg border-2 border-dashed border-gray-300 max-w-md mx-auto">
    <div class="text-center mb-4">
        <h2 class="text-xl font-bold text-gray-900">REÇU TEMPORAIRE</h2>
        <p class="text-xs text-red-600 font-semibold">⚠️ EN ATTENTE DE VALIDATION</p>
        <p class="text-xs text-gray-500">Ref: {{ $payment->reference }}</p>
    </div>

    <div class="border-t border-b border-gray-300 py-4 my-4 space-y-2">
        <div class="flex justify-between">
            <span class="text-gray-600">Client:</span>
            <span class="font-semibold">{{ $payment->client->full_name }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Montant:</span>
            <span class="font-bold text-2xl text-green-600">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Date:</span>
            <span class="font-semibold">{{ $payment->payment_date->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Agent:</span>
            <span class="font-semibold">{{ $payment->collector->name }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Méthode:</span>
            <span class="font-semibold">{{ strtoupper($payment->payment_method) }}</span>
        </div>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded p-3 text-xs text-center">
        <p class="font-semibold text-yellow-800">⚠️ IMPORTANT</p>
        <p class="text-yellow-700">Ce reçu sera validé par le bureau dans les 24h.</p>
        <p class="text-yellow-700">Conservez-le jusqu'à réception du reçu officiel.</p>
    </div>

    <div class="mt-4 text-center">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            📄 Imprimer pour le client
        </button>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-area, .print-area * {
        visibility: visible;
    }
    .print-area {
        position: absolute;
        left: 0;
        top: 0;
    }
}
</style>
