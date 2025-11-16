<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📒 Carnet Numérique - {{ $client->full_name }}
            </h2>
            <a href="{{ route('clients.index') }}" class="bg-gradient-to-r from-blue-600 to-yellow-500 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-yellow-600 transition">
                ← Retour aux Clients
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Informations Client --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="text-center mb-6">
                    @if($client->photo)
                        <img src="{{ asset('storage/' . $client->photo) }}" alt="{{ $client->full_name }}" class="w-24 h-24 rounded-full mx-auto mb-4 object-cover border-4 border-blue-200">
                    @else
                        <div class="w-24 h-24 rounded-full mx-auto mb-4 bg-gradient-to-br from-blue-400 to-yellow-400 flex items-center justify-center text-white text-3xl font-bold">
                            {{ substr($client->first_name, 0, 1) }}{{ substr($client->last_name, 0, 1) }}
                        </div>
                    @endif
                    <h3 class="text-xl font-bold text-gray-900">{{ $client->full_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $client->code }}</p>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">📱 Téléphone:</span>
                        <span class="font-semibold">{{ $client->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">📍 Ville:</span>
                        <span class="font-semibold">{{ $client->city ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">👤 Agent:</span>
                        <span class="font-semibold">{{ $client->agent->name }}</span>
                    </div>
                </div>
            </div>

            {{-- Carnet Physique --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-6">
                <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                    <span class="text-2xl mr-2">📓</span>
                    Carnet Physique (300 FCFA)
                </h4>

                @if($client->notebook_fully_paid)
                    <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg p-4 text-center">
                        <svg class="w-12 h-12 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-green-800 font-semibold">Carnet payé!</p>
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="bg-gradient-to-r from-blue-50 to-yellow-50 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600">Payé:</span>
                                <span class="text-lg font-bold text-blue-600">{{ number_format($client->notebook_amount_paid, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Reste:</span>
                                <span class="text-lg font-bold text-yellow-600">{{ number_format($client->notebook_remaining, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <div class="mt-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-yellow-500 h-2 rounded-full" style="width: {{ ($client->notebook_amount_paid / 300) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Formulaire de paiement --}}
                        <form method="POST" action="{{ route('notebooks.pay', $client) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Montant</label>
                                <input type="number" name="amount" max="{{ $client->notebook_remaining }}" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Max: {{ $client->notebook_remaining }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Note</label>
                                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Optionnel"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-yellow-500 text-white py-2 rounded-lg hover:from-blue-700 hover:to-yellow-600 transition font-semibold">
                                💰 Enregistrer le Paiement
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Historique des paiements de carnet --}}
                @if($client->notebookPayments->count() > 0)
                    <div class="mt-6">
                        <h5 class="font-semibold text-gray-900 mb-3">Historique des paiements</h5>
                        <div class="space-y-2">
                            @foreach($client->notebookPayments as $payment)
                                <div class="bg-gray-50 rounded-lg p-3 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="font-semibold text-blue-600">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</span>
                                        <span class="text-gray-500">{{ $payment->payment_date->format('d/m/Y') }}</span>
                                    </div>
                                    @if($payment->notes)
                                        <p class="text-gray-600 mt-1">{{ $payment->notes }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-1">Par: {{ $payment->user->name }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Carnet Numérique --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <span class="text-2xl mr-2">💻</span>
                    Carnet Numérique
                </h3>

                {{-- Résumé des paiements --}}
                @if($client->tontines->count() > 0)
                    @php
                        $totalTontinesAmount = $client->tontines->sum('total_amount');
                        $totalValidatedPayments = $client->payments->where('status', 'validated')->sum('amount');
                        $totalPendingPayments = $client->payments->where('status', 'pending')->sum('amount');
                        $totalAllPayments = $totalValidatedPayments + $totalPendingPayments;
                    @endphp
                    <div class="bg-gradient-to-r from-blue-50 to-yellow-50 rounded-lg p-4 mb-6 border border-blue-200">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Total à payer</p>
                                <p class="text-lg font-bold text-blue-700">{{ number_format($totalTontinesAmount, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Payé (Validé)</p>
                                <p class="text-lg font-bold text-green-700">{{ number_format($totalValidatedPayments, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-1">En attente</p>
                                <p class="text-lg font-bold text-yellow-700">{{ number_format($totalPendingPayments, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Reste à payer</p>
                                <p class="text-lg font-bold text-red-700">{{ number_format($totalTontinesAmount - $totalValidatedPayments, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Tontines du client --}}
                <div class="space-y-4">
                    @forelse($client->tontines as $tontine)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $tontine->product->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $tontine->product->code }}</p>
                                </div>
                                <div class="text-right">
                                    @if($tontine->status === 'active')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gradient-to-r from-blue-100 to-yellow-100 text-blue-800">
                                            🔄 En cours
                                        </span>
                                    @elseif($tontine->status === 'completed')
                                        @if($tontine->delivery_status === 'delivered')
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                ✅ Livré
                                            </span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                ⏳ En attente de livraison
                                            </span>
                                        @endif
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($tontine->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3 mb-3">
                                <div class="bg-blue-50 rounded-lg p-3">
                                    <p class="text-xs text-blue-600 mb-1">Prix Total</p>
                                    <p class="text-lg font-bold text-blue-900">{{ number_format($tontine->total_amount, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div class="bg-green-50 rounded-lg p-3">
                                    <p class="text-xs text-green-600 mb-1">Payé (Validé)</p>
                                    <p class="text-lg font-bold text-green-900">{{ number_format($tontine->amount_paid, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div class="bg-yellow-50 rounded-lg p-3">
                                    <p class="text-xs text-yellow-600 mb-1">Total Paiements</p>
                                    <p class="text-lg font-bold text-yellow-900">{{ number_format($tontine->total_payments_amount, 0, ',', ' ') }} FCFA</p>
                                </div>
                            </div>

                            {{-- Barre de progression --}}
                            <div class="mb-3">
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>Progression</span>
                                    <span>{{ number_format(($tontine->amount_paid / $tontine->total_amount) * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-yellow-500 h-2 rounded-full transition-all" style="width: {{ ($tontine->amount_paid / $tontine->total_amount) * 100 }}%"></div>
                                </div>
                            </div>

                            {{-- Paiements de cette tontine --}}
                            @php
                                $tontinePayments = $client->payments->where('tontine_id', $tontine->id);
                            @endphp
                            @if($tontinePayments->count() > 0)
                                <details class="mt-3">
                                    <summary class="cursor-pointer text-sm font-semibold text-blue-600 hover:text-blue-800">
                                        📋 Voir les {{ $tontinePayments->count() }} paiements
                                    </summary>
                                    <div class="mt-3 space-y-2 max-h-60 overflow-y-auto">
                                        @foreach($tontinePayments->sortByDesc('payment_date') as $payment)
                                            <div class="rounded p-3 text-sm flex justify-between items-center {{ $payment->status === 'validated' ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
                                                <div>
                                                    <span class="font-semibold {{ $payment->status === 'validated' ? 'text-green-700' : 'text-yellow-700' }}">
                                                        {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                                    </span>
                                                    <span class="text-gray-600 ml-2 text-xs">{{ $payment->payment_date->format('d/m/Y') }}</span>
                                                    @if($payment->notes)
                                                        <p class="text-gray-600 text-xs mt-1">{{ $payment->notes }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <span class="px-2 py-1 text-xs rounded-full font-medium {{ $payment->status === 'validated' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ $payment->status === 'validated' ? '✓ Validé' : '⏳ En attente' }}
                                                    </span>
                                                    @if($payment->status === 'validated')
                                                        <p class="text-xs text-gray-500 mt-1">Validé le {{ $payment->validated_at?->format('d/m/Y') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-4 text-gray-500">Aucune tontine pour ce client</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
