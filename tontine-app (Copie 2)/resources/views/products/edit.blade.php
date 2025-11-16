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
                    
                    {{-- Photo du produit --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Photo du produit</label>
                        
                        @if($product->photo)
                            <div class="mb-3">
                                <p class="text-sm text-gray-600 mb-2">Photo actuelle:</p>
                                <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                            </div>
                        @endif
                        
                        <input type="file" name="photo" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">
                            @if($product->photo)
                                Choisir une nouvelle photo pour remplacer l'actuelle (JPG, PNG, GIF - max 2MB)
                            @else
                                Format: JPG, PNG, GIF (max 2MB)
                            @endif
                        </p>
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
</x-app-layout>
