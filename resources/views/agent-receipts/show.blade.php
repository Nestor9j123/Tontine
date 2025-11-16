<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Reçu Mensuel - {{ $receiptData['agent']->name }}
                </h2>
                <p class="text-sm text-gray-600">{{ $receiptData['period']['period_name'] }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('agent-receipts.index', ['month' => $receiptData['period']['month'], 'year' => $receiptData['period']['year']]) }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                    ← Retour
                </a>
                <a href="{{ route('agent-receipts.pdf', ['agent' => $receiptData['agent'], 'month' => $receiptData['period']['month'], 'year' => $receiptData['period']['year']]) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    <i class="fas fa-download mr-1"></i> Télécharger PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 space-y-6">
        {{-- Résumé de performance --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Résumé de Performance</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ $receiptData['summary']['validated_payments'] }}</p>
                    <p class="text-sm text-blue-700">Paiements Collectés</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">{{ number_format($receiptData['summary']['total_amount_collected'], 0, ',', ' ') }}</p>
                    <p class="text-sm text-green-700">Montant Total (FCFA)</p>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <p class="text-2xl font-bold text-purple-600">{{ $receiptData['summary']['new_clients'] }}</p>
                    <p class="text-sm text-purple-700">Nouveaux Clients</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600">{{ number_format($receiptData['summary']['total_expenses'], 0, ',', ' ') }}</p>
                    <p class="text-sm text-red-700">Dépenses (FCFA)</p>
                </div>
            </div>
        </div>

        {{-- Détail des paiements par jour --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Paiements Collectés</h3>
            </div>
            <div class="p-6">
                @if($receiptData['payments']->count() > 0)
                    <div class="space-y-4">
                        @foreach($receiptData['payments'] as $date => $dailyPayments)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h4>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">{{ $dailyPayments->count() }} paiements</p>
                                        <p class="font-semibold text-green-600">{{ number_format($dailyPayments->sum('amount'), 0, ',', ' ') }} FCFA</p>
                                    </div>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($dailyPayments as $payment)
                                                <tr>
                                                    <td class="px-3 py-2 text-gray-900">{{ $payment->client->name }}</td>
                                                    <td class="px-3 py-2 text-gray-700">{{ $payment->tontine->product->name }}</td>
                                                    <td class="px-3 py-2 font-medium text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                                                    <td class="px-3 py-2">
                                                        @if($payment->status === 'validated')
                                                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Validé</span>
                                                        @elseif($payment->status === 'pending')
                                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">En attente</span>
                                                        @else
                                                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Rejeté</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">Aucun paiement collecté pour cette période.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Dépenses de l'agent --}}
        @if($receiptData['expenses']->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Dépenses de l'Agent</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($receiptData['expenses'] as $expense)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $expense->expense_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $expense->description }}</td>
                                    <td class="px-4 py-2 text-sm font-medium text-red-600">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">{{ $expense->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-right">
                    <p class="text-lg font-semibold text-red-600">
                        Total Dépenses: {{ number_format($receiptData['expenses']->sum('amount'), 0, ',', ' ') }} FCFA
                    </p>
                </div>
            </div>
        </div>
        @endif

        {{-- Statistiques additionnelles --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques Additionnelles</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-xl font-bold text-gray-700">{{ $receiptData['summary']['active_tontines'] }}</p>
                    <p class="text-sm text-gray-600">Tontines Actives</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-xl font-bold text-gray-700">{{ $receiptData['summary']['completed_tontines'] }}</p>
                    <p class="text-sm text-gray-600">Tontines Complétées</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-xl font-bold text-gray-700">{{ $receiptData['summary']['pending_payments'] }}</p>
                    <p class="text-sm text-gray-600">Paiements en Attente</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
