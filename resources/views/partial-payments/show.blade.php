<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Détails du Paiement {{ $payment->reference }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Client: {{ $payment->client->full_name }} • Agent: {{ $payment->collector->name }}
                </p>
            </div>
            <div class="flex space-x-3">
                @if($payment->remaining_missing_amount > 0)
                    <a href="{{ route('partial-payments.add-missing-form', $payment) }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Ajouter Paiement
                    </a>
                @endif
                <a href="{{ route('partial-payments.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 space-y-6">
            
            <!-- Informations du paiement -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations du Paiement</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Montant Payé</label>
                        <p class="text-2xl font-bold text-green-600">{{ $payment->formatted_amount }}</p>
                    </div>
                    
                    @if($payment->expected_amount)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Montant Attendu</label>
                        <p class="text-2xl font-bold text-blue-600">{{ $payment->formatted_expected_amount }}</p>
                    </div>
                    @endif
                    
                    @if($payment->missing_amount > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Montant Manquant</label>
                        <p class="text-2xl font-bold text-red-600">{{ $payment->formatted_remaining_missing_amount }}</p>
                    </div>
                    @endif
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Statut du Paiement</label>
                        {!! $payment->payment_status_badge !!}
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Date de Paiement</label>
                        <p class="text-gray-900">{{ $payment->payment_date->format('d/m/Y') }}</p>
                    </div>
                </div>

                @if($payment->notes)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Notes</label>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $payment->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Barre de progression -->
            @if($payment->expected_amount)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Progression du Paiement</h3>
                
                @php
                    $totalPaid = $payment->amount + $payment->missing_paid_amount;
                    $progressPercent = ($totalPaid / $payment->expected_amount) * 100;
                @endphp
                
                <div class="mb-4">
                    <div class="flex justify-between text-sm font-medium text-gray-700 mb-2">
                        <span>{{ number_format($totalPaid) }} FCFA payés</span>
                        <span>{{ number_format($payment->expected_amount) }} FCFA attendus</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-green-500 to-blue-500 h-3 rounded-full transition-all duration-300" 
                             style="width: {{ min(100, $progressPercent) }}%"></div>
                    </div>
                    <p class="text-center text-sm text-gray-600 mt-2">{{ number_format($progressPercent, 1) }}% complété</p>
                </div>
            </div>
            @endif

            <!-- Historique des paiements -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Historique des Paiements</h3>
                
                @if($history->count() > 0)
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($history as $entry)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white
                                                {{ $entry->action_type === 'initial_payment' ? 'bg-blue-500' : '' }}
                                                {{ $entry->action_type === 'missing_payment' ? 'bg-yellow-500' : '' }}
                                                {{ $entry->action_type === 'completion' ? 'bg-green-500' : '' }}
                                                {{ $entry->action_type === 'adjustment' ? 'bg-purple-500' : '' }}">
                                                @if($entry->action_type === 'initial_payment')
                                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4zM18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"></path>
                                                    </svg>
                                                @elseif($entry->action_type === 'completion')
                                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                                                    </svg>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                            <div>
                                                <p class="text-sm text-gray-900 font-medium">{{ $entry->action_type_label }}</p>
                                                <p class="text-sm text-gray-500">
                                                    Montant: {{ $entry->formatted_amount }}
                                                    @if($entry->remaining_amount > 0)
                                                        • Restant: {{ $entry->formatted_remaining_amount }}
                                                    @endif
                                                </p>
                                                @if($entry->notes)
                                                    <p class="text-sm text-gray-600 mt-1 italic">{{ $entry->notes }}</p>
                                                @endif
                                                <p class="text-xs text-gray-400">Enregistré par: {{ $entry->recordedBy->name }}</p>
                                            </div>
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                {{ $entry->action_date->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">Aucun historique disponible.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
