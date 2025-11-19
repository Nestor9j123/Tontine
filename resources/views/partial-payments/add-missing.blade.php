<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Ajouter un Paiement Manquant
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Paiement {{ $payment->reference }} • Client: {{ $payment->client->full_name }}
                </p>
            </div>
            <a href="{{ route('partial-payments.show', $payment) }}" 
               class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4">
            
            <!-- Récapitulatif du paiement -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Récapitulatif du Paiement</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-blue-700 mb-1">Montant Payé</label>
                        <p class="text-xl font-bold text-blue-900">{{ $payment->formatted_amount }}</p>
                    </div>
                    
                    @if($payment->expected_amount)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Montant Attendu</label>
                        <p class="text-xl font-bold text-gray-900">{{ $payment->formatted_expected_amount }}</p>
                    </div>
                    @endif
                    
                    <div class="bg-red-50 p-4 rounded-lg">
                        <label class="block text-sm font-medium text-red-700 mb-1">Montant Manquant</label>
                        <p class="text-xl font-bold text-red-900">{{ $payment->formatted_remaining_missing_amount }}</p>
                    </div>
                </div>

                @if($payment->missing_paid_amount > 0)
                <div class="mt-4 bg-green-50 p-4 rounded-lg">
                    <label class="block text-sm font-medium text-green-700 mb-1">Déjà Payé sur le Manquant</label>
                    <p class="text-lg font-bold text-green-900">{{ $payment->formatted_missing_paid_amount }}</p>
                </div>
                @endif
            </div>

            <!-- Formulaire d'ajout -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ajouter un Paiement</h3>
                
                <form method="POST" action="{{ route('partial-payments.store-missing', $payment) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Montant à Ajouter *
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   name="amount" 
                                   id="amount" 
                                   step="0.01" 
                                   min="0.01" 
                                   max="{{ $payment->remaining_missing_amount }}"
                                   value="{{ old('amount') }}" 
                                   class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all @error('amount') border-red-500 @enderror"
                                   placeholder="Entrez le montant"
                                   required>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">FCFA</span>
                            </div>
                        </div>
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">
                            Maximum: {{ $payment->formatted_remaining_missing_amount }}
                        </p>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (optionnel)
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="4" 
                                  class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all @error('notes') border-red-500 @enderror"
                                  placeholder="Ajoutez des notes sur ce paiement (carnet, détails, etc.)">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Prévisualisation -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Prévisualisation</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-700">Montant actuel payé:</span>
                                <span class="font-medium text-blue-900">{{ $payment->formatted_amount }}</span>
                            </div>
                            <div>
                                <span class="text-blue-700">Déjà payé sur manquant:</span>
                                <span class="font-medium text-blue-900">{{ $payment->formatted_missing_paid_amount }}</span>
                            </div>
                        </div>
                        <div class="mt-2 pt-2 border-t border-blue-200">
                            <span class="text-blue-700">Total après ce paiement:</span>
                            <span class="font-bold text-blue-900" id="total-preview">{{ $payment->formatted_amount }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t">
                        <a href="{{ route('partial-payments.show', $payment) }}" 
                           class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Ajouter le Paiement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Calcul en temps réel du total
        document.getElementById('amount').addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            const currentAmount = {{ $payment->amount }};
            const missingPaidAmount = {{ $payment->missing_paid_amount }};
            const total = currentAmount + missingPaidAmount + amount;
            
            document.getElementById('total-preview').textContent = 
                new Intl.NumberFormat('fr-FR').format(total) + ' FCFA';
        });
    </script>
</x-app-layout>
