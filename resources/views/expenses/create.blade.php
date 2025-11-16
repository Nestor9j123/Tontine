<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Nouvelle Charge Mensuelle
                </h2>
                <p class="text-sm text-gray-600">Enregistrer une nouvelle charge ou dépense</p>
            </div>
            <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                ← Retour aux charges
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto py-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('expenses.store') }}">
                @csrf
                
                <div class="space-y-6">
                    {{-- Type de charge --}}
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de charge <span class="text-red-500">*</span>
                        </label>
                        <select name="type" id="type" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror"
                                onchange="toggleAgentField()">
                            <option value="">Sélectionner un type</option>
                            <option value="rent" {{ old('type') === 'rent' ? 'selected' : '' }}>Loyer</option>
                            <option value="electricity" {{ old('type') === 'electricity' ? 'selected' : '' }}>Électricité</option>
                            <option value="agent_expense" {{ old('type') === 'agent_expense' ? 'selected' : '' }}>Dépense Agent</option>
                            <option value="general" {{ old('type') === 'general' ? 'selected' : '' }}>Autre / Général</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Agent concerné (si dépense agent) --}}
                    <div id="agentField" style="display: none;">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Agent concerné <span class="text-red-500">*</span>
                        </label>
                        <select name="user_id" id="user_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('user_id') border-red-500 @enderror">
                            <option value="">Sélectionner un agent</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ old('user_id') == $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Obligatoire pour une dépense de type "Dépense Agent".</p>
                        @error('user_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                                  placeholder="Décrivez la nature de cette charge...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Montant --}}
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Montant (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="amount" id="amount" required min="0" step="0.01"
                               value="{{ old('amount') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror"
                               placeholder="Ex: 25000">
                        @error('amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date" id="date" required
                               value="{{ old('date', date('Y-m-d')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('date') border-red-500 @enderror">
                        @error('date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (optionnel)
                        </label>
                        <textarea name="notes" id="notes" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror"
                                  placeholder="Informations supplémentaires...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('expenses.index') }}" 
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                        Enregistrer la Charge
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleAgentField() {
            const typeSelect = document.getElementById('type');
            const agentField = document.getElementById('agentField');
            const userSelect = document.getElementById('user_id');
            
            if (!typeSelect || !agentField || !userSelect) return;

            if (typeSelect.value === 'agent_expense') {
                agentField.style.display = 'block';
                userSelect.setAttribute('required', 'required');
            } else {
                agentField.style.display = 'none';
                userSelect.removeAttribute('required');
                userSelect.value = '';
            }
        }

        // Initialiser l'affichage au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            toggleAgentField();
        });
    </script>
</x-app-layout>
