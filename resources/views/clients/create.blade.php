<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Nouveau Client') }}
            </h2>
            <a href="{{ route('clients.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à la liste
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-blue-50 to-yellow-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Informations du Client</h3>
                <p class="text-sm text-gray-600 mt-1">Remplissez tous les champs obligatoires (*)</p>
            </div>

            <form method="POST" action="{{ route('clients.store') }}" enctype="multipart/form-data" class="p-6">
                @csrf
                
                {{-- Afficher les erreurs globales --}}
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h4 class="text-red-800 font-semibold">Erreurs de validation</h4>
                        </div>
                        <ul class="list-disc list-inside text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Prénom --}}
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('first_name') border-red-500 @enderror">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nom --}}
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('last_name') border-red-500 @enderror">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="+237 6XX XXX XXX"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Téléphone secondaire --}}
                    <div>
                        <label for="phone_secondary" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone Secondaire
                        </label>
                        <input type="tel" name="phone_secondary" id="phone_secondary" value="{{ old('phone_secondary') }}" placeholder="+237 6XX XXX XXX"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="client@example.com"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ville --}}
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                            Ville
                        </label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}" placeholder="Yaoundé"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- Numéro CNI --}}
                    <div>
                        <label for="id_card_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Numéro CNI
                        </label>
                        <input type="text" name="id_card_number" id="id_card_number" value="{{ old('id_card_number') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- Photo --}}
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                            Photo
                        </label>
                        <input type="file" name="photo" id="photo" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Max: 2MB</p>
                    </div>
                </div>

                {{-- Adresse complète --}}
                <div class="mt-6">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse Complète
                    </label>
                    <textarea name="address" id="address" rows="3" placeholder="Quartier, rue, etc."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('address') }}</textarea>
                </div>

                {{-- Agent (visible seulement pour admin/secrétaire) --}}
                @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('secretary'))
                <div class="mt-6">
                    <label for="agent_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Agent Responsable <span class="text-red-500">*</span>
                    </label>
                    <select name="agent_id" id="agent_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sélectionner un agent</option>
                        @foreach(\App\Models\User::role('agent')->get() as $agent)
                            <option value="{{ $agent->id }}" {{ old('agent_id') == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('agent_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                {{-- Section Paiements Existants --}}
                <div class="mt-8 p-6 bg-yellow-50 border border-yellow-200 rounded-lg" x-data="{ hasExistingPayments: {{ old('has_existing_payments') ? 'true' : 'false' }} }">
                    <div class="flex items-center mb-4">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h4 class="text-lg font-semibold text-yellow-800">Paiements Existants</h4>
                    </div>
                    <p class="text-sm text-yellow-700 mb-4">
                        Si ce client payait déjà avant l'utilisation du système (ex: déjà à 8 mois sur 12), cochez cette case et remplissez les informations.
                    </p>
                    
                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="has_existing_payments" value="1" 
                                {{ old('has_existing_payments') ? 'checked' : '' }}
                                x-model="hasExistingPayments"
                                class="rounded border-gray-300 text-yellow-600 shadow-sm focus:ring-yellow-500">
                            <span class="ml-2 text-sm text-yellow-800 font-medium">Ce client a des paiements existants</span>
                        </label>
                    </div>

                    <div x-show="hasExistingPayments" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Nombre de paiements déjà effectués --}}
                        <div>
                            <label for="existing_payments_count" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de paiements déjà effectués
                            </label>
                            <input type="number" name="existing_payments_count" id="existing_payments_count" 
                                value="{{ old('existing_payments_count') }}" min="1" max="36"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                placeholder="8">
                            @error('existing_payments_count')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Montant déjà payé --}}
                        <div>
                            <label for="existing_payments_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Montant total déjà payé (FCFA)
                            </label>
                            <input type="number" name="existing_payments_amount" id="existing_payments_amount" 
                                value="{{ old('existing_payments_amount') }}" min="0"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                placeholder="40000">
                            @error('existing_payments_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Date de début des paiements --}}
                        <div>
                            <label for="existing_payments_start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date de début des paiements
                            </label>
                            <input type="date" name="existing_payments_start_date" id="existing_payments_start_date" 
                                value="{{ old('existing_payments_start_date') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            @error('existing_payments_start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label for="existing_payments_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notes sur les paiements existants
                            </label>
                            <textarea name="existing_payments_notes" id="existing_payments_notes" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                                placeholder="Détails sur les paiements déjà effectués...">{{ old('existing_payments_notes') }}</textarea>
                            @error('existing_payments_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Statut --}}
                <div class="mt-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Client actif</span>
                    </label>
                </div>

                {{-- Boutons --}}
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('clients.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-yellow-500 text-white rounded-lg hover:from-blue-700 hover:to-yellow-600 transition">
                        Enregistrer le Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
