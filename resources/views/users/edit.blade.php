<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier l'Utilisateur</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                        <input type="text" name="name" value="{{ $user->name }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="{{ $user->email }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                        <input type="tel" name="phone" value="{{ $user->phone }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <!-- Photo de l'utilisateur -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Photo de profil</label>
                        
                        @if($user->photo)
                            <div class="mb-3">
                                <p class="text-sm text-gray-600 mb-2">Photo actuelle:</p>
                                <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="w-20 h-20 object-cover rounded-full border-2 border-gray-300">
                            </div>
                        @endif
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition-colors" x-data="userPhotoUpload()">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div class="mt-2">
                                <label for="photo" class="cursor-pointer">
                                    <span class="text-sm font-medium text-gray-900">
                                        @if($user->photo) Changer la photo @else Ajouter une photo @endif
                                    </span>
                                    <input type="file" 
                                           id="photo" 
                                           name="photo" 
                                           accept="image/*" 
                                           class="hidden" 
                                           @change="handleFile($event)">
                                </label>
                                <p class="mt-1 text-xs text-gray-500">
                                    JPG, PNG, GIF, WebP jusqu'à 2MB
                                </p>
                            </div>
                            
                            <!-- Prévisualisation -->
                            <div x-show="selectedFile" class="mt-4">
                                <div class="relative inline-block">
                                    <img :src="selectedFile?.url" alt="Aperçu" class="w-20 h-20 object-cover rounded-full border-2 border-gray-200">
                                    <button type="button" 
                                            @click="removeFile()"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 text-sm hover:bg-red-600">×</button>
                                </div>
                                <p class="mt-2 text-sm text-green-600" x-text="`Nouvelle photo : ${selectedFile?.name}`"></p>
                            </div>
                        </div>
                        @error('photo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                        <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">Utilisateur actif</span>
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <a href="{{ route('users.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Annuler</a>
                    <button type="submit" class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function userPhotoUpload() {
            return {
                selectedFile: null,

                handleFile(event) {
                    const file = event.target.files[0];
                    
                    if (!file) return;
                    
                    // Validation avec helper
                    if (!window.validateImageFile(file)) {
                        this.clearInput();
                        return;
                    }
                    
                    // Créer URL pour prévisualisation
                    const fileUrl = URL.createObjectURL(file);
                    
                    this.selectedFile = {
                        file: file,
                        name: file.name,
                        url: fileUrl,
                        size: window.formatFileSize(file.size)
                    };
                    
                    window.safeShowSuccess(`Nouvelle photo sélectionnée : ${file.name}`);
                },

                removeFile() {
                    // Nettoyer l'URL de l'objet
                    if (this.selectedFile?.url) {
                        URL.revokeObjectURL(this.selectedFile.url);
                    }
                    
                    this.selectedFile = null;
                    this.clearInput();
                },

                clearInput() {
                    const input = document.getElementById('photo');
                    if (input) {
                        input.value = '';
                    }
                }
            };
        }
    </script>
</x-app-layout>
