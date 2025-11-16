<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier la Tontine') }}
            </h2>
            <a href="{{ route('tontines.show', $tontine) }}" class="text-gray-600 hover:text-gray-900">
                Retour
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-yellow-50 to-orange-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Modification de la Tontine</h3>
                <p class="text-sm text-gray-600 mt-1">Code: {{ $tontine->code }}</p>
            </div>

            <form method="POST" action="{{ route('tontines.update', $tontine) }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Statut --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Statut <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="active" {{ $tontine->status === 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="completed" {{ $tontine->status === 'completed' ? 'selected' : '' }}>Terminé</option>
                            <option value="suspended" {{ $tontine->status === 'suspended' ? 'selected' : '' }}>Suspendu</option>
                            <option value="cancelled" {{ $tontine->status === 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes / Observations
                        </label>
                        <textarea name="notes" id="notes" rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('notes', $tontine->notes) }}</textarea>
                    </div>
                </div>

                {{-- Boutons --}}
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('tontines.show', $tontine) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-lg hover:from-yellow-600 hover:to-orange-600 transition">
                        Mettre à Jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
