<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    @if(isset($showingLowStock) && $showingLowStock)
                        <span class="text-red-600">⚠️ Produits en Stock Faible</span>
                    @else
                        Produits de Tontine
                    @endif
                </h2>
                @if(isset($showingLowStock) && $showingLowStock)
                    <p class="text-sm text-gray-600 mt-1">
                        Produits en rupture ou stock faible (≤ 10 unités)
                        <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 ml-2">→ Voir tous</a>
                    </p>
                @endif
            </div>
            @if(auth()->user()->hasRole('secretary') || auth()->user()->hasRole('super_admin'))
                <a href="{{ route('products.create') }}" class="bg-gradient-to-r from-blue-600 to-yellow-500 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-yellow-600 transition">
                    + Nouveau Produit
                </a>
            @endif
        </div>
    </x-slot>

    {{-- Formulaire de filtrage --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <form action="{{ route('products.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Rechercher</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        placeholder="Nom ou description...">
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" id="type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Tous les types</option>
                        <option value="daily" {{ request('type') === 'daily' ? 'selected' : '' }}>Journalier</option>
                        <option value="weekly" {{ request('type') === 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="monthly" {{ request('type') === 'monthly' ? 'selected' : '' }}>Mensuel</option>
                        <option value="yearly" {{ request('type') === 'yearly' ? 'selected' : '' }}>Annuel</option>
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="status" id="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Trier par</label>
                    <select name="sort" id="sort" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date de création</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom</option>
                        <option value="price" {{ request('sort') === 'price' ? 'selected' : '' }}>Prix</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-end space-x-4">
                    <div class="flex-1">
                        <label for="min_price" class="block text-sm font-medium text-gray-700 mb-1">Prix minimum</label>
                        <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            placeholder="FCFA">
                    </div>
                    <div class="flex-1">
                        <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Prix maximum</label>
                        <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            placeholder="FCFA">
                    </div>
                </div>
                
                <div class="flex items-end space-x-4">
                    <div class="flex-1">
                        <label for="direction" class="block text-sm font-medium text-gray-700 mb-1">Ordre</label>
                        <select name="direction" id="direction" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="desc" {{ request('direction') === 'desc' || !request('direction') ? 'selected' : '' }}>Décroissant</option>
                            <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Croissant</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label for="per_page" class="block text-sm font-medium text-gray-700 mb-1">Par page</label>
                        <select name="per_page" id="per_page" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="15" {{ request('per_page') == 15 || !request('per_page') ? 'selected' : '' }}>15</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-end space-x-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex-1">Filtrer</button>
                    @if(request()->hasAny(['search', 'type', 'status', 'min_price', 'max_price', 'sort', 'direction', 'per_page']))
                        <a href="{{ route('products.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition flex-1 text-center">Réinitialiser</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition
                    @if($product->stock_quantity !== null && $product->stock_quantity <= 10)
                        @if($product->stock_quantity == 0)
                            border-2 border-red-500 animate-pulse
                        @else
                            border-2 border-orange-400 animate-pulse
                        @endif
                    @else
                        border border-gray-100
                    @endif">
            {{-- Photos du produit --}}
            @php
                $primaryPhoto = $product->primaryPhoto ?? $product->photos->first();
            @endphp
            
            @if($primaryPhoto)
                <div class="h-48 bg-gray-100 overflow-hidden relative">
                    <img src="{{ asset('storage/' . $primaryPhoto->path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @if($product->photos->count() > 1)
                        <div class="absolute bottom-2 right-2 bg-black bg-opacity-60 text-white text-xs px-2 py-1 rounded-full">
                            +{{ $product->photos->count() - 1 }} photos
                        </div>
                    @endif
                </div>
            @elseif($product->photo)
                {{-- Ancienne photo (compatibilité) --}}
                <div class="h-48 bg-gray-100 overflow-hidden">
                    <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                </div>
            @else
                <div class="h-48 bg-gradient-to-br from-blue-100 to-yellow-100 flex items-center justify-center">
                    <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            @endif
            
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ $product->name }}</h3>
                    @if($product->is_active)
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Actif</span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">Inactif</span>
                    @endif
                </div>
                <p class="text-gray-600 text-sm mb-4">{{ $product->description }}</p>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Prix:</span>
                        <span class="font-bold text-blue-600">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Durée:</span>
                        <span class="font-semibold">{{ $product->formatted_duration }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Type:</span>
                        <span class="font-semibold">
                            @if($product->type === 'daily') Journalier
                            @elseif($product->type === 'weekly') Hebdomadaire
                            @elseif($product->type === 'yearly') Annuel
                            @else Mensuel @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-sm">Stock disponible:</span>
                        <span class="font-semibold">
                            @if($product->stock_quantity === null)
                                <span class="text-green-600">♾️ Illimité</span>
                            @else
                                <span class="text-orange-600">{{ $product->stock_quantity }}</span>
                                @if($product->stock_quantity <= 10 && $product->stock_quantity > 0)
                                    <span class="text-red-500 text-xs ml-1 animate-pulse">⚠️ Stock faible</span>
                                @elseif($product->stock_quantity == 0)
                                    <span class="text-red-600 text-xs ml-1 animate-bounce">❌ Rupture</span>
                                @endif
                            @endif
                        </span>
                    </div>
                </div>
                @if(auth()->user()->hasRole('super_admin'))
                    <div class="flex space-x-2">
                        <a href="{{ route('products.edit', $product) }}" class="flex-1 bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition text-center text-sm">
                            Modifier
                        </a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" id="delete-product-{{ $product->id }}" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDeleteProduct({{ $product->id }}, '{{ $product->name }}')" class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition text-sm">
                                Supprimer
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    
    {{-- Pagination --}}
    @if($products->hasPages() || $products->total() > 0)
    <div class="mt-6">
        <x-pagination-info :paginator="$products" />
    </div>
    @endif

    <script>
        function confirmDeleteProduct(productId, productName) {
            showConfirm(
                'Supprimer ce produit',
                `Êtes-vous sûr de vouloir supprimer le produit "${productName}" ? Cette action est irréversible et affectera toutes les tontines liées à ce produit.`,
                () => {
                    showInfo('Suppression en cours...', 'Suppression du produit...');
                    document.getElementById(`delete-product-${productId}`).submit();
                },
                'danger',
                'Supprimer définitivement',
                'Annuler'
            );
        }
    </script>
</x-app-layout>
