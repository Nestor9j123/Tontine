<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Détails de la Tontine') }}
            </h2>
            <div class="flex space-x-2">
                @can('create_payments')
                <a href="{{ route('payments.create', ['tontine_id' => $tontine->id]) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nouveau Paiement
                </a>
                @endcan
                <a href="{{ route('tontines.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        {{-- En-tête tontine --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-50 to-yellow-50 border-b border-gray-200">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $tontine->product->name }}</h3>
                        <p class="text-gray-600 mt-1">Code: <span class="font-mono font-semibold">{{ $tontine->code }}</span></p>
                        <p class="text-gray-600">Client: <span class="font-semibold">{{ $tontine->client->full_name }}</span></p>
                    </div>
                    <div>
                        @if($tontine->status === 'active')
                            <span class="px-4 py-2 bg-green-100 text-green-800 text-sm font-semibold rounded-full">Actif</span>
                        @elseif($tontine->status === 'completed')
                            <span class="px-4 py-2 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">Terminé</span>
                        @elseif($tontine->status === 'suspended')
                            <span class="px-4 py-2 bg-orange-100 text-orange-800 text-sm font-semibold rounded-full">Suspendu</span>
                        @else
                            <span class="px-4 py-2 bg-red-100 text-red-800 text-sm font-semibold rounded-full">Annulé</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Montant Total</h4>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($tontine->total_amount, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Montant Payé</h4>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($tontine->paid_amount, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Montant Restant</h4>
                        <p class="text-2xl font-bold text-orange-600">{{ number_format($tontine->remaining_amount, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Date de Début</h4>
                        <p class="text-lg text-gray-900">{{ $tontine->start_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Date de Fin</h4>
                        <p class="text-lg text-gray-900">{{ $tontine->end_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Agent</h4>
                        <p class="text-lg text-gray-900">{{ $tontine->agent->name }}</p>
                    </div>
                </div>

                {{-- Barre de progression --}}
                <div class="mt-6">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-sm font-medium text-gray-700">Progression</h4>
                        <span class="text-sm font-semibold text-gray-900">{{ $tontine->progress_percentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-4 rounded-full transition-all duration-500" style="width: {{ $tontine->progress_percentage }}%"></div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">{{ $tontine->completed_payments }} / {{ $tontine->total_payments }} paiements effectués</p>
                </div>

                @if($tontine->notes)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Notes</h4>
                    <p class="text-gray-900">{{ $tontine->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Historique des paiements --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Historique des Paiements</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Méthode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Collecté par</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tontine->payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $payment->reference }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $payment->payment_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($payment->payment_method === 'cash')
                                    Espèces
                                @elseif($payment->payment_method === 'mobile_money')
                                    Mobile Money
                                @else
                                    Virement
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $payment->collector->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($payment->status === 'validated')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Validé</span>
                                @elseif($payment->status === 'pending')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">En attente</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Rejeté</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900">Voir</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                Aucun paiement enregistré
                                @can('create_payments')
                                <br>
                                <a href="{{ route('payments.create', ['tontine_id' => $tontine->id]) }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                                    Enregistrer le premier paiement →
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
