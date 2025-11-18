<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Modifier le Produit</h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom *</label>
                        <input type="text" name="name" value="{{ $product->name }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ $product->description }}</textarea>
                    </div>
                    
                    {{-- Photos du produit --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Photos du produit</label>
                        
                        @if($product->photos && $product->photos->count() > 0)
                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Photos actuelles ({{ $product->photos->count() }}):</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    @foreach($product->photos as $photo)
                                        <div class="relative group">
                                            <img src="{{ asset('storage/' . $photo->path) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="w-full h-24 object-cover rounded-lg border border-gray-300">
                                            @if($photo->is_primary)
                                                <span class="absolute top-1 left-1 bg-green-500 text-white text-xs px-2 py-1 rounded">
                                                    Principal
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors" x-data="photoUpload()">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <div class="mt-2">
                                <label for="photos" class="cursor-pointer">
                                    <span class="text-sm font-medium text-gray-900">
                                        Ajouter de nouvelles photos
                                    </span>
                                    <input type="file" 
                                           id="photos" 
                                           name="photos[]" 
                                           accept="image/*" 
                                           multiple 
                                           class="hidden" 
                                           @change="handleFiles($event)">
                                </label>
                                <p class="mt-1 text-xs text-gray-500">
                                    JPG, PNG, GIF, WebP jusqu'à 2MB chacune
                                </p>
                            </div>
                            
                            <!-- Prévisualisation des nouvelles photos -->
                            <div x-show="selectedFiles.length > 0" class="mt-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Nouvelles photos à ajouter:</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    <template x-for="(file, index) in selectedFiles" :key="index">
                                        <div class="relative">
                                            <img :src="file.url" :alt="file.name" class="w-full h-20 object-cover rounded border">
                                            <button type="button" 
                                                    @click="removeFile(index)"
                                                    class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs hover:bg-red-600">×</button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix (FCFA) *</label>
                        <input type="number" name="price" value="{{ $product->price }}" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Durée *</label>
                            <input type="number" name="duration_value" value="{{ $product->duration_value ?? 1 }}" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unité *</label>
                            <select name="duration_unit" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="days" {{ ($product->duration_unit ?? 'months') === 'days' ? 'selected' : '' }}>Jours</option>
                                <option value="weeks" {{ ($product->duration_unit ?? 'months') === 'weeks' ? 'selected' : '' }}>Semaines</option>
                                <option value="months" {{ ($product->duration_unit ?? 'months') === 'months' ? 'selected' : '' }}>Mois</option>
                                <option value="years" {{ ($product->duration_unit ?? 'months') === 'years' ? 'selected' : '' }}>Années</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de paiement *</label>
                        <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="daily" {{ $product->type === 'daily' ? 'selected' : '' }}>Journalier (paiement chaque jour)</option>
                            <option value="weekly" {{ $product->type === 'weekly' ? 'selected' : '' }}>Hebdomadaire (paiement chaque semaine)</option>
                            <option value="monthly" {{ $product->type === 'monthly' ? 'selected' : '' }}>Mensuel (paiement chaque mois)</option>
                            <option value="yearly" {{ $product->type === 'yearly' ? 'selected' : '' }}>Annuel (paiement unique par an)</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Fréquence à laquelle le client effectue les paiements</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock disponible</label>
                            <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Laisser vide pour un stock illimité">
                            <p class="mt-1 text-xs text-gray-500">Nombre de places en stock (vide = illimité)</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Statut du produit *</label>
                            <div class="space-y-2 mt-2">
                                <label class="flex items-center">
                                    <input type="radio" name="status" value="active" {{ $product->is_active ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">✅ Actif</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="status" value="inactive" {{ !$product->is_active ? 'checked' : '' }} class="text-gray-600 focus:ring-gray-500">
                                    <span class="ml-2 text-sm text-gray-700">⏸️ Inactif</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-4">
                    <a href="{{ route('products.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">Annuler</a>
                    <button type="submit" class="px-6 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function photoUpload() {
            return {
                selectedFiles: [],

                handleFiles(event) {
                    const files = Array.from(event.target.files);
                    
                    // Vider la liste précédente
                    this.selectedFiles = [];
                    
                    files.forEach(file => {
                        // Validation avec helper
                        if (!window.validateImageFile(file)) {
                            return;
                        }
                        
                        // Créer URL pour prévisualisation
                        const fileUrl = URL.createObjectURL(file);
                        
                        this.selectedFiles.push({
                            file: file,
                            name: file.name,
                            url: fileUrl,
                            size: window.formatFileSize(file.size)
                        });
                    });
                    
                    if (this.selectedFiles.length > 0) {
                        window.safeShowSuccess(`${this.selectedFiles.length} photo(s) sélectionnée(s)`);
                    }
                },

                removeFile(index) {
                    // Nettoyer l'URL de l'objet
                    if (this.selectedFiles[index] && this.selectedFiles[index].url) {
                        URL.revokeObjectURL(this.selectedFiles[index].url);
                    }
                    
                    this.selectedFiles.splice(index, 1);
                    
                    // Réinitialiser l'input file si plus aucun fichier
                    if (this.selectedFiles.length === 0) {
                        const input = document.getElementById('photos');
                        if (input) {
                            input.value = '';
                        }
                    }
                }
            };
        }
    </script>
</x-app-layout>
