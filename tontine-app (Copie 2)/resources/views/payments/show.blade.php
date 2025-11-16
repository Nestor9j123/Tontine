<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails du Paiement') }}
            </h2>
            <div class="flex space-x-2">
                @if($payment->status === 'pending' && (auth()->user()->hasRole('secretary') || auth()->user()->hasRole('super_admin')))
                <form method="POST" action="{{ route('payments.validate', $payment) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition">
                        ✓ Valider
                    </button>
                </form>
                <button onclick="rejectPayment({{ $payment->id }})" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                    ✗ Rejeter
                </button>
                @endif
                <a href="{{ route('payments.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        {{-- Informations principales --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Paiement {{ $payment->reference }}</h3>
                        <p class="text-gray-600 mt-1">{{ $payment->payment_date->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div>
                        @if($payment->status === 'validated')
                            <span class="px-4 py-2 bg-green-100 text-green-800 text-sm font-semibold rounded-full">✓ Validé</span>
                        @elseif($payment->status === 'pending')
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">⏳ En attente</span>
                        @else
                            <span class="px-4 py-2 bg-red-100 text-red-800 text-sm font-semibold rounded-full">✗ Rejeté</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Montant</h4>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</p>
                        @if($payment->is_multiple_payment)
                            <div class="mt-2 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <p class="text-sm text-blue-700 font-medium">📅 Paiement Multiple</p>
                                <p class="text-xs text-blue-600 mt-1">
                                    {{ $payment->days_count }} jours × {{ number_format($payment->daily_amount, 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Méthode de Paiement</h4>
                        <p class="text-lg text-gray-900">
                            @if($payment->payment_method === 'cash')
                                💵 Espèces
                            @elseif($payment->payment_method === 'mobile_money')
                                📱 Mobile Money
                            @else
                                🏦 Virement Bancaire
                            @endif
                        </p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Client</h4>
                        <p class="text-lg text-gray-900">{{ $payment->client->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $payment->client->phone }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Tontine</h4>
                        <p class="text-lg text-gray-900">{{ $payment->tontine->code }}</p>
                        <p class="text-sm text-gray-500">{{ $payment->tontine->product->name }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Collecté par</h4>
                        <p class="text-lg text-gray-900">{{ $payment->collector->name }}</p>
                    </div>
                    @if($payment->transaction_id)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">ID Transaction</h4>
                        <p class="text-lg text-gray-900 font-mono">{{ $payment->transaction_id }}</p>
                    </div>
                    @endif
                    @if($payment->validated_by)
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Validé par</h4>
                        <p class="text-lg text-gray-900">{{ $payment->validator->name }}</p>
                        <p class="text-sm text-gray-500">{{ $payment->validated_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    @endif
                </div>

                @if($payment->notes)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Notes</h4>
                    <p class="text-gray-900">{{ $payment->notes }}</p>
                </div>
                @endif

                @if($payment->rejection_reason)
                <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <h4 class="text-sm font-medium text-red-700 mb-2">Raison du Rejet</h4>
                    <p class="text-red-900">{{ $payment->rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Reçu de paiement --}}
        @if($payment->status === 'validated')
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Reçu de Paiement</h3>
                <a href="{{ route('reports.export.payment.pdf', $payment) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    📄 Télécharger PDF
                </a>
            </div>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                <p class="text-gray-600">Cliquez sur le bouton ci-dessus pour télécharger le reçu en PDF</p>
            </div>
        </div>
        @endif
    </div>

    <script>
        function rejectPayment(paymentId) {
            showPrompt(
                'Rejeter ce paiement',
                'Veuillez indiquer la raison du rejet de ce paiement. Cette action est irréversible.',
                (reason) => {
                    if (reason && reason.trim()) {
                        showInfo('Rejet en cours...', 'Traitement de la demande de rejet...');
                        
                        fetch(`/payments/${paymentId}/reject`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ rejection_reason: reason })
                        })
                        .then(response => {
                            if (response.ok) {
                                showSuccess('Paiement rejeté', 'Le paiement a été rejeté avec succès.');
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                showError('Erreur', 'Une erreur est survenue lors du rejet du paiement.');
                            }
                        })
                        .catch(error => {
                            showError('Erreur réseau', 'Impossible de communiquer avec le serveur.');
                        });
                    } else {
                        showWarning('Raison requise', 'Vous devez indiquer une raison pour rejeter ce paiement.');
                    }
                },
                'Raison du rejet (ex: Montant incorrect, Document manquant...)',
                'text',
                'danger',
                'Rejeter le paiement',
                'Annuler'
            );
        }
    </script>
</x-app-layout>
