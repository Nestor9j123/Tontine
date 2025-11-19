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
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all"
                        placeholder="Nom ou description...">
                </div>
                
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" id="type" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                        <option value="">Tous les types</option>
                        <option value="daily" {{ request('type') === 'daily' ? 'selected' : '' }}>Journalier</option>
                        <option value="weekly" {{ request('type') === 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="monthly" {{ request('type') === 'monthly' ? 'selected' : '' }}>Mensuel</option>
                        <option value="yearly" {{ request('type') === 'yearly' ? 'selected' : '' }}>Annuel</option>
                    </select>
                </div>
                
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select name="status" id="status" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Trier par</label>
                    <select name="sort" id="sort" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Date de création</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom</option>
                        <option value="price" {{ request('sort') === 'price' ? 'selected' : '' }}>Prix</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex items-end space-x-4">
                    <div class="flex-1">
                        <label for="min_price" class="block text-sm font-medium text-gray-700 mb-2">Prix minimum</label>
                        <input type="number" name="min_price" id="min_price" value="{{ request('min_price') }}" 
                            class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all"
                            placeholder="FCFA">
                    </div>
                    <div class="flex-1">
                        <label for="max_price" class="block text-sm font-medium text-gray-700 mb-2">Prix maximum</label>
                        <input type="number" name="max_price" id="max_price" value="{{ request('max_price') }}" 
                            class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all"
                            placeholder="FCFA">
                    </div>
                </div>
                
                <div class="flex items-end space-x-4">
                    <div class="flex-1">
                        <label for="direction" class="block text-sm font-medium text-gray-700 mb-2">Ordre</label>
                        <select name="direction" id="direction" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                            <option value="desc" {{ request('direction') === 'desc' || !request('direction') ? 'selected' : '' }}>Décroissant</option>
                            <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Croissant</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label for="per_page" class="block text-sm font-medium text-gray-700 mb-2">Par page</label>
                        <select name="per_page" id="per_page" class="w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:ring-opacity-50 transition-all">
                            <option value="15" {{ request('per_page') == 15 || !request('per_page') ? 'selected' : '' }}>15</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-end space-x-4">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition flex-1 font-medium flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>Filtrer</span>
                    </button>
                    <a href="{{ route('products.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 transition flex-1 text-center font-medium flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Effacer</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($products as $product)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden group hover:shadow-lg transition-shadow duration-300
                    @if(isset($showingLowStock) && $showingLowStock)
                        @if($product->stock_quantity == 0)
                            border-2 border-red-500 animate-pulse
                        @else
                            border-2 border-orange-400 animate-pulse
                        @endif
                    @else
                        border border-gray-100 hover:border-blue-200
                    @endif">
            {{-- Photos du produit --}}
            @php
                $primaryPhoto = $product->primaryPhoto ?? $product->photos->first();
            @endphp
            
            
            @if($product->photos->count() > 0)
                @if($product->photos->count() == 1)
                    {{-- Produit avec une seule photo - pas d'animation --}}
                    <a href="{{ route('products.show', $product) }}" class="block">
                        <div style="height: 192px !important; background: red !important; border: 5px solid blue !important; overflow: hidden !important; position: relative !important;">
                            <!-- Test sans IMG -->
                            <div style="width: 100% !important; height: 100% !important; background: linear-gradient(45deg, #4CAF50, #2196F3) !important; display: flex !important; align-items: center !important; justify-content: center !important; color: white !important; font-size: 18px !important; font-weight: bold !important;">
                                PHOTO {{ $product->name }}
                            </div>
                            
                            <!-- IMG cachée pour test -->
                            <img src="{{ asset('storage/' . $product->photos->first()->path) }}" 
                                 alt="{{ $product->name }}" 
                                 style="position: absolute !important; top: 0 !important; left: 0 !important; width: 100% !important; height: 100% !important; object-fit: cover !important; display: block !important; opacity: 1 !important; visibility: visible !important; z-index: 999 !important;"
                                 onload="console.log('✅ IMG loaded'); this.style.opacity='1';"
                                 onerror="console.log('❌ IMG failed'); this.style.display='none';">
                        </div>
                    </a>
                @else
                    {{-- Produit avec plusieurs photos - carrousel --}}
                    <a href="{{ route('products.show', $product) }}" class="block">
                        <div style="height: 192px !important; background: #f3f4f6 !important; overflow: hidden !important; position: relative !important;" class="product-carousel" data-product="{{ $product->id }}">
                            @foreach($product->photos as $index => $photo)
                                <!-- Div avec animation slide -->
                                <div style="width: 100% !important; height: 100% !important; position: absolute !important; top: 0 !important; background-image: url('{{ asset('storage/' . $photo->path) }}') !important; background-size: cover !important; background-position: center !important; background-repeat: no-repeat !important; transform: translateX({{ $index === 0 ? '0%' : '100%' }}) !important; transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) !important;"
                                     class="carousel-image"
                                     data-index="{{ $index }}">
                                </div>
                            @endforeach
                            
                            <!-- Compteur -->
                            <div class="absolute bottom-2 right-2 bg-black bg-opacity-60 text-white text-xs px-2 py-1 rounded-full flex items-center space-x-1">
                                <span class="current-index">1</span>
                                <span>/</span>
                                <span>{{ $product->photos->count() }}</span>
                            </div>
                            
                            <!-- Indicateurs -->
                            <div class="absolute bottom-2 left-2 flex space-x-1">
                                @foreach($product->photos as $index => $photo)
                                    <div class="indicator w-2 h-2 rounded-full transition-all duration-300 cursor-pointer {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}" 
                                         data-index="{{ $index }}"></div>
                                @endforeach
                            </div>
                        </div>
                    </a>
                @endif
            @elseif($product->photo)
                {{-- Ancienne photo (compatibilité) --}}
                <a href="{{ route('products.show', $product) }}" class="block">
                    <div class="h-48 bg-gray-100 overflow-hidden relative">
                        <img src="{{ asset('storage/' . $product->photo) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all duration-300 flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="bg-white text-gray-900 px-3 py-1 rounded-full text-sm font-medium">
                                    👁️ Voir détails
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @else
                <a href="{{ route('products.show', $product) }}" class="block">
                    <div class="h-48 bg-gradient-to-br from-blue-100 to-yellow-100 flex items-center justify-center relative group-hover:from-blue-200 group-hover:to-yellow-200 transition-all duration-300">
                        <svg class="w-16 h-16 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300 flex items-center justify-center">
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="bg-white text-gray-900 px-3 py-1 rounded-full text-sm font-medium">
                                    👁️ Voir détails
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @endif
            
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <a href="{{ route('products.show', $product) }}" class="hover:text-blue-600 transition-colors">
                        <h3 class="text-lg font-bold text-gray-900">{{ $product->name }}</h3>
                    </a>
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
                
                <!-- Bouton Voir Détails -->
                <div class="mb-4">
                    <a href="{{ route('products.show', $product) }}" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-purple-700 transition text-center text-sm font-medium flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span>
                            @if($product->photos->count() > 1)
                                Voir {{ $product->photos->count() }} photos
                            @elseif($product->photos->count() == 1)
                                Voir la photo
                            @else
                                Voir détails
                            @endif
                        </span>
                    </a>
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

        // Carrousel de photos simple
        document.addEventListener('DOMContentLoaded', function() {
            const carousels = document.querySelectorAll('.product-carousel');
            
            carousels.forEach(carousel => {
                const images = carousel.querySelectorAll('.carousel-image');
                const indicators = carousel.querySelectorAll('.indicator');
                const currentIndexDisplay = carousel.querySelector('.current-index');
                
                if (images.length <= 1) return;
                
                let currentIndex = 0;
                let interval;
                let paused = false;
                
                // Fonction pour afficher une image avec animation slide élégante
                function showImage(index) {
                    console.log(`🎬 Slide animation to image ${index}`);
                    
                    // Masquer toutes les images à droite (translateX(100%))
                    images.forEach((img, i) => {
                        if (i === index) {
                            img.style.transform = 'translateX(0%)'; // Image courante au centre
                        } else if (i < index) {
                            img.style.transform = 'translateX(-100%)'; // Images précédentes à gauche
                        } else {
                            img.style.transform = 'translateX(100%)'; // Images suivantes à droite
                        }
                    });
                    
                    // Désactiver tous les indicateurs
                    indicators.forEach(ind => {
                        ind.classList.remove('bg-white');
                        ind.classList.add('bg-white/50');
                    });
                    
                    // Activer l'indicateur courant
                    if (indicators[index]) {
                        indicators[index].classList.remove('bg-white/50');
                        indicators[index].classList.add('bg-white');
                    }
                    
                    // Mettre à jour le compteur
                    if (currentIndexDisplay) {
                        currentIndexDisplay.textContent = index + 1;
                    }
                    
                    currentIndex = index;
                }
                
                // Fonction pour passer à l'image suivante
                function nextImage() {
                    const nextIndex = (currentIndex + 1) % images.length;
                    console.log(`🔄 Carrousel ${carousel.dataset.product}: ${currentIndex} → ${nextIndex}`);
                    showImage(nextIndex);
                }
                
                // Démarrer l'animation avec un délai aléatoire (plus long pour apprécier l'animation)
                function startCarousel() {
                    const delay = 3500 + (Math.random() * 2000); // 3.5 à 5.5 secondes
                    const initialDelay = Math.random() * 2000; // 0 à 2 secondes
                    
                    setTimeout(() => {
                        interval = setInterval(() => {
                            if (!paused) {
                                nextImage();
                            }
                        }, delay);
                    }, initialDelay);
                }
                
                // Events pour pause/resume
                carousel.addEventListener('mouseenter', () => {
                    paused = true;
                });
                
                carousel.addEventListener('mouseleave', () => {
                    paused = false;
                });
                
                // Click sur les indicateurs
                indicators.forEach((indicator, index) => {
                    indicator.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        showImage(index);
                    });
                });
                
                // Démarrer le carrousel
                startCarousel();
            });
        });
    </script>
</x-app-layout>
