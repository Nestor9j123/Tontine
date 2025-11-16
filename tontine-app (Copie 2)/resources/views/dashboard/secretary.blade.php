{{-- Dashboard Secrétaire --}}

{{-- Statistiques principales --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    {{-- Total Clients --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Clients</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_clients'] }}</h3>
                <p class="text-green-600 text-xs mt-1 font-medium">{{ $stats['active_clients'] }} actifs</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Paiements en attente --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Paiements en Attente</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['pending_payments'] }}</h3>
                <p class="text-orange-600 text-xs mt-1 font-medium">À valider</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-3">
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Paiements validés --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Paiements Validés</p>
                <h3 class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['validated_payments'] }}</h3>
                <p class="text-green-600 text-xs mt-1 font-medium">{{ $stats['today_payments'] }} aujourd'hui</p>
            </div>
            <div class="bg-green-50 rounded-lg p-3">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    {{-- Montant Total --}}
    <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium">Montant Collecté</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['total_amount_collected'], 0, ',', ' ') }}</h3>
                <p class="text-gray-600 text-xs mt-1">FCFA</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-3">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Alerte paiements en attente --}}
@if($stats['pending_payments'] > 0)
<div class="bg-gradient-to-r from-orange-50 to-orange-100 border-l-4 border-orange-500 p-6 rounded-lg mb-6">
    <div class="flex items-center">
        <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <div>
            <h4 class="text-orange-900 font-semibold">{{ $stats['pending_payments'] }} paiement(s) en attente de validation</h4>
            <p class="text-orange-700 text-sm">Montant total : {{ number_format($stats['pending_amount'], 0, ',', ' ') }} FCFA</p>
        </div>
        <a href="{{ route('payments.index') }}?status=pending" class="ml-auto bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
            Valider maintenant
        </a>
    </div>
</div>
@endif

{{-- Paiements en attente de validation --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">Paiements à Valider</h3>
        <a href="{{ route('payments.index') }}?status=pending" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            Voir tout →
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stats['pending_validations'] as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->reference }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->client->full_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->collector->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex space-x-2">
                            <form method="POST" action="{{ route('payments.validate', $payment) }}">
                                @csrf
                                <button type="submit" class="bg-green-100 text-green-700 px-3 py-1 rounded-lg hover:bg-green-200 transition text-xs font-medium">
                                    Valider
                                </button>
                            </form>
                            <button onclick="rejectPayment({{ $payment->id }})" class="bg-red-100 text-red-700 px-3 py-1 rounded-lg hover:bg-red-200 transition text-xs font-medium">
                                Rejeter
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Aucun paiement en attente
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Paiements récents validés --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="p-6 border-b border-gray-100">
        <h3 class="text-lg font-semibold text-gray-900">Paiements Récents Validés</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Validé par</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($stats['recent_payments'] as $payment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $payment->reference }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->client->full_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->collector->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $payment->validator ? $payment->validator->name : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">Aucun paiement récent</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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
