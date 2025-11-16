<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nouvelle Tontine') }}
            </h2>
            <a href="{{ route('tontines.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-50 to-yellow-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Créer une Nouvelle Tontine</h3>
                <p class="text-sm text-gray-600 mt-1">Les montants seront calculés automatiquement</p>
            </div>

            <form method="POST" action="{{ route('tontines.store') }}" class="p-6" x-data="tontineForm()">
                @csrf

                <div class="space-y-6">
                    {{-- Client --}}
                    <div>
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Client <span class="text-red-500">*</span>
                        </label>
                        <select name="client_id" id="client_id" required x-model="clientId"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('client_id') border-red-500 @enderror">
                            <option value="">Sélectionner un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ $selectedClientId == $client->id ? 'selected' : '' }}>
                                    {{ $client->full_name }} - {{ $client->phone }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Produit --}}
                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Produit de Tontine <span class="text-red-500">*</span>
                        </label>
                        <select name="product_id" id="product_id" required x-model="productId" @change="updateProductInfo()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('product_id') border-red-500 @enderror">
                            <option value="">Sélectionner un produit</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                    data-price="{{ $product->price }}"
                                    data-duration="{{ $product->duration_months }}"
                                    data-type="{{ $product->type }}">
                                    {{ $product->name }} - {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Aperçu du produit --}}
                    <div x-show="productId" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 mb-2">Détails du Produit</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-700">Montant Total:</span>
                                <span class="font-bold text-blue-900" x-text="formatPrice(price)"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">Durée:</span>
                                <span class="font-bold text-blue-900" x-text="duration + ' mois'"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">Type:</span>
                                <span class="font-bold text-blue-900" x-text="getTypeLabel(type)"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">Paiements estimés:</span>
                                <span class="font-bold text-blue-900" x-text="estimatedPayments"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Date de début --}}
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date de Début <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('start_date') border-red-500 @enderror">
                        @error('start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes / Observations
                        </label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Informations complémentaires..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('tontines.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-yellow-500 text-white rounded-lg hover:from-blue-700 hover:to-yellow-600 transition">
                        Créer la Tontine
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function tontineForm() {
            return {
                clientId: '{{ $selectedClientId ?? '' }}',
                productId: '',
                price: 0,
                duration: 0,
                type: '',
                estimatedPayments: 0,
                
                updateProductInfo() {
                    const select = document.getElementById('product_id');
                    const option = select.options[select.selectedIndex];
                    
                    if (option.value) {
                        this.price = parseFloat(option.dataset.price);
                        this.duration = parseInt(option.dataset.duration);
                        this.type = option.dataset.type;
                        this.calculatePayments();
                    }
                },
                
                calculatePayments() {
                    switch(this.type) {
                        case 'daily':
                            this.estimatedPayments = this.duration * 30;
                            break;
                        case 'weekly':
                            this.estimatedPayments = this.duration * 4;
                            break;
                        case 'monthly':
                            this.estimatedPayments = this.duration;
                            break;
                    }
                },
                
                formatPrice(price) {
                    return new Intl.NumberFormat('fr-FR').format(price) + ' FCFA';
                },
                
                getTypeLabel(type) {
                    const labels = {
                        'daily': 'Journalier',
                        'weekly': 'Hebdomadaire',
                        'monthly': 'Mensuel'
                    };
                    return labels[type] || type;
                }
            }
        }
    </script>
</x-app-layout>
