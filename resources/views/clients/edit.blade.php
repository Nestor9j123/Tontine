<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Modifier le Client') }}
            </h2>
            <a href="{{ route('clients.show', $client) }}" class="text-gray-600 hover:text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 bg-gradient-to-r from-yellow-50 to-orange-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Modification de {{ $client->full_name }}</h3>
                <p class="text-sm text-gray-600 mt-1">Code: {{ $client->code }}</p>
            </div>

            <form method="POST" action="{{ route('clients.update', $client) }}" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Prénom --}}
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $client->first_name) }}" required
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
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $client->last_name) }}" required
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
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-600 text-sm">
                                +228
                            </span>
                            <input type="tel" name="phone_digits" id="phone_digits" 
                                   value="{{ old('phone_digits', $client->phone ? preg_replace('/^\+228(\d{2})(\d{2})(\d{2})(\d{2})$/', '$1 $2 $3 $4', $client->phone) : '') }}" 
                                   required placeholder="12 34 56 78" maxlength="11" pattern="[0-9]{2}\s[0-9]{2}\s[0-9]{2}\s[0-9]{2}"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                                   x-data="{ formatPhone(event) { 
                                       let value = event.target.value.replace(/\D/g, '');
                                       if (value.length > 8) value = value.slice(0, 8);
                                       if (value.length >= 2) value = value.slice(0,2) + ' ' + value.slice(2);
                                       if (value.length >= 6) value = value.slice(0,5) + ' ' + value.slice(5);
                                       if (value.length >= 9) value = value.slice(0,8) + ' ' + value.slice(8);
                                       event.target.value = value;
                                   }}"
                                   @input="formatPhone($event); $el.nextElementSibling.value = '+228' + $event.target.value.replace(/\s/g, '')"
                                   @blur="if($event.target.value.replace(/\D/g, '').length !== 8) { $event.target.setCustomValidity('Le numéro doit contenir exactement 8 chiffres'); } else { $event.target.setCustomValidity(''); }"
                                   @invalid="$event.target.setCustomValidity('Le numéro doit contenir exactement 8 chiffres')"
                                   minlength="11">
                            <input type="hidden" name="phone" x-data="{}" 
                                   x-init="$nextTick(() => { $el.value = '+228' + $el.previousElementSibling.value.replace(/\s/g, ''); });"
                                   x-on:input.window="if($event.target === $el.previousElementSibling) { $el.value = '+228' + $event.target.value.replace(/\s/g, ''); }">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Format: 12 34 56 78 (8 chiffres)</p>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Téléphone secondaire --}}
                    <div>
                        <label for="phone_secondary" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone Secondaire
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-600 text-sm">
                                +228
                            </span>
                            <input type="tel" name="phone_secondary_digits" id="phone_secondary_digits" 
                                   value="{{ old('phone_secondary_digits', $client->phone_secondary ? preg_replace('/^\+228(\d{2})(\d{2})(\d{2})(\d{2})$/', '$1 $2 $3 $4', $client->phone_secondary) : '') }}" 
                                   placeholder="12 34 56 78" maxlength="11" pattern="[0-9]{2}\s[0-9]{2}\s[0-9]{2}\s[0-9]{2}"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   x-data="{ formatPhone(event) { 
                                       let value = event.target.value.replace(/\D/g, '');
                                       if (value.length > 8) value = value.slice(0, 8);
                                       if (value.length >= 2) value = value.slice(0,2) + ' ' + value.slice(2);
                                       if (value.length >= 6) value = value.slice(0,5) + ' ' + value.slice(5);
                                       if (value.length >= 9) value = value.slice(0,8) + ' ' + value.slice(8);
                                       event.target.value = value;
                                   }}"
                                   @input="formatPhone($event); $el.nextElementSibling.value = $event.target.value ? '+228' + $event.target.value.replace(/\s/g, '') : ''"
                                   @blur="if($event.target.value && $event.target.value.replace(/\D/g, '').length !== 8) { $event.target.setCustomValidity('Le numéro doit contenir exactement 8 chiffres'); } else { $event.target.setCustomValidity(''); }"
                                   @invalid="$event.target.setCustomValidity('Le numéro doit contenir exactement 8 chiffres')">
                            <input type="hidden" name="phone_secondary" x-data="{}" 
                                   x-init="$nextTick(() => { $el.value = $el.previousElementSibling.value ? '+228' + $el.previousElementSibling.value.replace(/\s/g, '') : ''; });"
                                   x-on:input.window="if($event.target === $el.previousElementSibling) { $el.value = $event.target.value ? '+228' + $event.target.value.replace(/\s/g, '') : ''; }">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Optionnel - Format: 12 34 56 78 (8 chiffres)</p>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $client->email) }}"
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
                        <input type="text" name="city" id="city" value="{{ old('city', $client->city) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- Numéro CNI --}}
                    <div>
                        <label for="id_card_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Numéro CNI
                        </label>
                        <input type="text" name="id_card_number" id="id_card_number" value="{{ old('id_card_number', $client->id_card_number) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    {{-- Photo --}}
                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                            Photo
                        </label>
                        <input type="file" name="photo" id="photo" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @if($client->photo)
                            <p class="mt-1 text-xs text-gray-500">Photo actuelle disponible</p>
                        @endif
                    </div>
                </div>

                {{-- Adresse complète --}}
                <div class="mt-6">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse Complète
                    </label>
                    <textarea name="address" id="address" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('address', $client->address) }}</textarea>
                </div>

                {{-- Agent (visible seulement pour admin/secrétaire) --}}
                @if(auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('secretary'))
                <div class="mt-6">
                    <label for="agent_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Agent Responsable <span class="text-red-500">*</span>
                    </label>
                    <select name="agent_id" id="agent_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach(\App\Models\User::role('agent')->get() as $agent)
                            <option value="{{ $agent->id }}" {{ old('agent_id', $client->agent_id) == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Statut --}}
                <div class="mt-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $client->is_active) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Client actif</span>
                    </label>
                </div>

                {{-- Boutons --}}
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('clients.show', $client) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
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
