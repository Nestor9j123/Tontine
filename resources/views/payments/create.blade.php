<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Enregistrer un Paiement') }}
            </h2>
            <a href="{{ route('payments.index') }}" class="text-gray-600 hover:text-gray-900">
                Retour
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Nouveau Paiement</h3>
                <p class="text-sm text-gray-600 mt-1">Enregistrer un paiement pour une tontine</p>
            </div>

            <form method="POST" action="{{ route('payments.store') }}" x-data="paymentForm()" @submit.prevent="validatePaymentForm($event)">
                @csrf

                <div class="space-y-6">
                    {{-- Agent Collecteur (Secrétaire/Super Admin seulement) --}}
                    @if(auth()->user()->hasAnyRole(['secretary', 'super_admin']))
                    <div>
                        <label for="collected_by" class="block text-sm font-medium text-gray-700 mb-2">
                            Agent qui a apporté les fiches <span class="text-red-500">*</span>
                        </label>
                        <select name="collected_by" id="collected_by" required x-model="collectedBy" @change="loadAgentClients()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Sélectionner un agent</option>
                            @php
                                $agents = \App\Models\User::role('agent')->where('is_active', true)->orderBy('name')->get(['id', 'name']);
                            @endphp
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                            @endforeach
                        </select>
                        @error('collected_by')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            💡 Sélectionnez d'abord l'agent pour charger ses clients
                        </p>
                    </div>
                    @endif

                    {{-- Client --}}
                    <div x-show="agentClients.length > 0 || !{{ auth()->user()->hasAnyRole(['secretary', 'super_admin']) ? 'true' : 'false' }}">
                        <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Client <span class="text-red-500">*</span>
                        </label>
                        <select name="client_id" id="client_id" required x-model="clientId" @change="loadTontines()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">
                                @if(auth()->user()->hasAnyRole(['secretary', 'super_admin']))
                                    <span x-show="!collectedBy">Sélectionnez d'abord un agent</span>
                                    <span x-show="collectedBy && agentClients.length === 0">Chargement des clients...</span>
                                    <span x-show="collectedBy && agentClients.length > 0">Sélectionner un client</span>
                                @else
                                    Sélectionner un client
                                @endif
                            </option>
                            @if(!auth()->user()->hasAnyRole(['secretary', 'super_admin']))
                                @php
                                    // Filtrer les clients selon le rôle
                                    if (auth()->user()->hasRole('agent')) {
                                        $clients = \App\Models\Client::byAgent(auth()->id())->active()->get();
                                    } else {
                                        $clients = \App\Models\Client::active()->get();
                                    }
                                @endphp
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->first_name }} {{ $client->last_name }} ({{ $client->phone }})
                                    </option>
                                @endforeach
                            @else
                                <template x-for="client in agentClients" :key="client.id">
                                    <option :value="client.id" x-text="client.first_name + ' ' + client.last_name + ' (' + client.phone + ')'"></option>
                                </template>
                            @endif
                        </select>
                        @error('client_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tontine --}}
                    <div x-show="clientId">
                        <label for="tontine_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Tontine <span class="text-red-500">*</span>
                        </label>
                        <select name="tontine_id" id="tontine_id" required x-model="tontineId" @change="updateTontineInfo()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">
                                <span x-show="!tontines.length">Chargement des tontines...</span>
                                <span x-show="tontines.length === 0">Aucune tontine active</span>
                                <span x-show="tontines.length > 0">Sélectionner une tontine</span>
                            </option>
                            <template x-for="tontine in tontines" :key="tontine.id">
                                <option :value="tontine.id" x-text="'Tontine #' + tontine.id + ' - ' + tontine.product_name"></option>
                            </template>
                        </select>
                        @error('tontine_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        <!-- Debug info -->
                        <div x-show="clientId" class="mt-2 text-xs text-gray-500">
                            <span x-text="'Tontines chargées: ' + tontines.length"></span>
                        </div>
                    </div>

                    {{-- Info Tontine --}}
                    <div x-show="selectedTontine" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-900 mb-2">Informations de la Tontine</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-700">Montant Total:</span>
                                <span class="font-bold text-blue-900" x-text="formatPrice(selectedTontine?.total_amount)"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">Montant Payé:</span>
                                <span class="font-bold text-green-600" x-text="formatPrice(selectedTontine?.paid_amount)"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">Montant Restant:</span>
                                <span class="font-bold text-orange-600" x-text="formatPrice(selectedTontine?.remaining_amount)"></span>
                            </div>
                            <div>
                                <span class="text-blue-700">Progression:</span>
                                <span class="font-bold text-blue-900" x-text="selectedTontine?.progress_percentage + '%'"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Type de paiement --}}
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Type de Paiement <span class="text-red-500">*</span>
                        </label>
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" x-model="paymentType" value="single" class="mr-2">
                                <span class="text-sm">Paiement Simple</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" x-model="paymentType" value="multiple" class="mr-2">
                                <span class="text-sm">Paiement Multiple (plusieurs jours)</span>
                            </label>
                        </div>
                    </div>

                    {{-- Paiement Simple --}}
                    <div x-show="paymentType === 'single'" class="space-y-4">
                        <!-- Montant Attendu (optionnel) -->
                        <div>
                            <label for="expected_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Montant Attendu (Prix Normal) 
                                <span class="text-gray-500 text-xs">- Optionnel</span>
                            </label>
                            <input type="number" name="expected_amount" id="expected_amount" step="1" min="1" x-model="expectedAmount"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="Ex: 5000 FCFA (prix normal à payer)"
                                @input="calculateMissingAmount()">
                            <p class="mt-1 text-xs text-gray-500">Laissez vide si le client paie le montant exact</p>
                        </div>

                        <!-- Montant Payé -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Montant Payé <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="amount" id="single_amount" step="1" min="1" x-model="amount" 
                                :required="paymentType === 'single'"
                                x-bind:disabled="paymentType !== 'single'"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                placeholder="Montant réellement payé (ex: 3000)"
                                @input="calculateMissingAmount()">
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alerte Paiement Partiel -->
                        <div x-show="missingAmount > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-yellow-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <h4 class="text-sm font-medium text-yellow-800">⚠️ Paiement Partiel Détecté</h4>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        <strong>Montant manquant: <span x-text="formatPrice(missingAmount)"></span></strong>
                                    </p>
                                    <p class="text-xs text-yellow-600 mt-1">
                                        Ce paiement sera enregistré comme "partiel" et pourra être complété ultérieurement.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Paiement Multiple --}}
                    <div x-show="paymentType === 'multiple'" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="daily_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Montant Quotidien <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="daily_amount_input" x-model="dailyAmount" @input="calculateTotal()" step="1" min="1" 
                                    :required="paymentType === 'multiple'"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="Ex: 1000">
                            </div>
                            <div>
                                <label for="days_count" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre de Jours <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="days_count_input" x-model="daysCount" @input="calculateTotal()" max="365" step="1" min="1" 
                                    :required="paymentType === 'multiple'"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="Ex: 10">
                            </div>
                        </div>
                        
                        {{-- Calcul automatique --}}
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200" x-show="dailyAmount && daysCount">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-green-700">Montant Total:</span>
                                <span class="text-lg font-bold text-green-900" x-text="formatPrice(dailyAmount * daysCount)"></span>
                            </div>
                            <div class="text-xs text-green-600 mt-1">
                                <span x-text="daysCount"></span> jours × <span x-text="formatPrice(dailyAmount)"></span> = 
                                <span x-text="formatPrice(dailyAmount * daysCount)"></span>
                            </div>
                        </div>

                        {{-- Champs cachés pour le montant total - paiement multiple seulement --}}
                        <input type="hidden" name="daily_amount" x-bind:value="dailyAmount" x-bind:disabled="paymentType === 'single'">
                        <input type="hidden" name="days_count" x-bind:value="daysCount" x-bind:disabled="paymentType === 'single'">
                        {{-- Champ amount pour paiement multiple --}}
                        <input type="hidden" name="amount" x-bind:value="dailyAmount * daysCount" x-bind:disabled="paymentType === 'single'">
                    </div>

                    {{-- Date de paiement automatique (aujourd'hui) --}}
                    <input type="hidden" name="payment_date" value="{{ date('Y-m-d') }}">
                    
                    {{-- Affichage informatif de la date --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <span class="text-sm font-medium text-blue-900">Date de Paiement :</span>
                                <span class="text-sm text-blue-700 ml-2">{{ \Carbon\Carbon::now()->format('d/m/Y') }} (Aujourd'hui)</span>
                            </div>
                        </div>
                    </div>

                    {{-- Méthode de paiement --}}
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Méthode de Paiement <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_method" id="payment_method" required x-model="paymentMethod"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="cash">💵 Espèces</option>
                            <option value="mobile_money">📱 Mobile Money</option>
                            <option value="bank_transfer">🏦 Virement Bancaire</option>
                        </select>
                    </div>

                    {{-- Transaction ID (pour mobile money et virement) --}}
                    <div x-show="paymentMethod !== 'cash'">
                        <label for="transaction_id" class="block text-sm font-medium text-gray-700 mb-2">
                            ID de Transaction
                        </label>
                        <input type="text" name="transaction_id" id="transaction_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Ex: TXN123456789">
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes / Observations
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Informations complémentaires..."></textarea>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('payments.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition">
                        Enregistrer le Paiement
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function paymentForm() {
            return {
                collectedBy: '',
                agentClients: [],
                clientId: '{{ request('client_id') ?? '' }}',
                tontineId: '{{ request('tontine_id') ?? '' }}',
                tontines: [],
                selectedTontine: null,
                amount: '',
                expectedAmount: '',
                missingAmount: 0,
                paymentMethod: 'cash',
                paymentType: 'single',
                dailyAmount: '',
                daysCount: '',
                
                init() {
                    if (this.clientId) {
                        this.loadTontines();
                    }
                },
                
                async loadTontines() {
                    if (!this.clientId) return;
                    
                    console.log('Chargement des tontines pour client:', this.clientId);
                    
                    try {
                        const response = await fetch(`/api/clients/${this.clientId}/tontines`, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        
                        console.log('Réponse API:', response.status);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        console.log('Données reçues:', data);
                        
                        this.tontines = data;
                        
                        if (this.tontineId) {
                            this.updateTontineInfo();
                        }
                    } catch (error) {
                        console.error('Erreur lors du chargement des tontines:', error);
                        showError('Erreur de chargement', 'Impossible de charger les produits. Veuillez rafraîchir la page.');
                    }
                },
                
                updateTontineInfo() {
                    this.selectedTontine = this.tontines.find(t => t.id == this.tontineId);
                },
                
                calculateTotal() {
                    if (this.paymentType === 'multiple' && this.dailyAmount && this.daysCount) {
                        this.amount = this.dailyAmount * this.daysCount;
                    }
                },
                
                calculateMissingAmount() {
                    const expected = parseFloat(this.expectedAmount || 0);
                    const paid = parseFloat(this.amount || 0);
                    this.missingAmount = expected > paid ? expected - paid : 0;
                },

                async loadAgentClients() {
                    if (!this.collectedBy) {
                        this.agentClients = [];
                        this.clientId = '';
                        this.tontines = [];
                        this.selectedTontine = null;
                        return;
                    }
                    
                    console.log('Chargement des clients pour agent:', this.collectedBy);
                    
                    try {
                        const response = await fetch(`/api/agents/${this.collectedBy}/clients`, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            credentials: 'same-origin'
                        });
                        
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const data = await response.json();
                        this.agentClients = data;
                        
                        // Réinitialiser les selections
                        this.clientId = '';
                        this.tontines = [];
                        this.selectedTontine = null;
                        
                        console.log('Clients chargés:', this.agentClients.length);
                    } catch (error) {
                        console.error('Erreur lors du chargement des clients:', error);
                        showError('Erreur de chargement', 'Impossible de charger les clients de cet agent.');
                        this.agentClients = [];
                    }
                },
                
                validatePaymentForm(event) {
                    // Validation du client
                    if (!this.clientId) {
                        event.preventDefault();
                        showError('Client requis', 'Veuillez sélectionner un client.');
                        return false;
                    }
                    
                    // Validation de la tontine
                    if (!this.tontineId) {
                        event.preventDefault();
                        showError('Tontine requise', 'Veuillez sélectionner une tontine.');
                        return false;
                    }
                    
                    // Validation du montant selon le type de paiement
                    if (this.paymentType === 'single') {
                        const amount = parseFloat(this.amount || 0);
                        if (isNaN(amount) || amount < 1) {
                            event.preventDefault();
                            showError('Montant invalide', 'Veuillez saisir un montant d\'au moins 1 FCFA.');
                            return false;
                        }
                        
                        // Vérifier que le montant ne dépasse pas le restant
                        if (this.selectedTontine && amount > this.selectedTontine.remaining_amount) {
                            event.preventDefault();
                            showError('Montant trop élevé', `Le montant ne peut pas dépasser le restant à payer (${this.formatPrice(this.selectedTontine.remaining_amount)}).`);
                            return false;
                        }
                    } else if (this.paymentType === 'multiple') {
                        const dailyAmount = parseFloat(this.dailyAmount || 0);
                        const daysCount = parseInt(this.daysCount || 0);
                        
                        if (isNaN(dailyAmount) || dailyAmount < 1) {
                            event.preventDefault();
                            showError('Montant quotidien invalide', 'Veuillez saisir un montant quotidien d\'au moins 1 FCFA.');
                            return false;
                        }
                        
                        if (isNaN(daysCount) || daysCount < 1) {
                            event.preventDefault();
                            showError('Nombre de jours invalide', 'Veuillez saisir un nombre de jours d\'au moins 1.');
                            return false;
                        }
                        
                        if (daysCount > 365) {
                            event.preventDefault();
                            showError('Nombre de jours trop élevé', 'Le nombre de jours ne peut pas dépasser 365.');
                            return false;
                        }
                        
                        const totalAmount = dailyAmount * daysCount;
                        if (this.selectedTontine && totalAmount > this.selectedTontine.remaining_amount) {
                            event.preventDefault();
                            showError('Montant total trop élevé', `Le montant total ne peut pas dépasser le restant à payer (${this.formatPrice(this.selectedTontine.remaining_amount)}).`);
                            return false;
                        }
                    }
                    
                    // Si tout est valide, on soumet le formulaire
                    showInfo('Enregistrement en cours...', 'Veuillez patienter pendant l\'enregistrement du paiement.');
                    
                    // Soumettre le formulaire manuellement
                    setTimeout(() => {
                        event.target.submit();
                    }, 100);
                    
                    return true;
                },
                
                formatPrice(price) {
                    if (!price) return '0 FCFA';
                    return new Intl.NumberFormat('fr-FR').format(price) + ' FCFA';
                }
            }
        }
    </script>
</x-app-layout>
