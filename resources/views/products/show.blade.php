<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $product->name }}</h2>
            </div>
            @can('update', $product)
                <a href="{{ route('products.edit', $product) }}" class="bg-gradient-to-r from-blue-600 to-yellow-500 text-white px-6 py-2 rounded-lg hover:from-blue-700 hover:to-yellow-600 transition">
                    ✏️ Modifier
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Informations Produit -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-6">
                
                <!-- Galerie d'Images -->
                <div class="space-y-4">
                    @if($product->photos->count() > 0)
                        <!-- Image Principale -->
                        <div x-data="{ currentImage: 0, images: {{ $product->photos->pluck('path')->toJson() }} }" class="space-y-4">
                            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                <img x-bind:src="`{{ asset('storage/') }}/${images[currentImage]}`" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover transition-all duration-300">
                            </div>
                            
                            <!-- Miniatures -->
                            @if($product->photos->count() > 1)
                                <div class="grid grid-cols-4 gap-2">
                                    @foreach($product->photos as $index => $photo)
                                        <button @click="currentImage = {{ $index }}" 
                                                x-bind:class="currentImage === {{ $index }} ? 'ring-2 ring-blue-500' : 'hover:ring-1 hover:ring-gray-300'"
                                                class="aspect-square bg-gray-100 rounded-lg overflow-hidden transition-all">
                                            <img src="{{ asset('storage/' . $photo->path) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="w-full h-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Pas d'images -->
                        <div class="aspect-square bg-gradient-to-br from-blue-100 to-yellow-100 rounded-lg flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-20 h-20 text-blue-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-blue-600 font-medium">Aucune image</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Détails Produit -->
                <div class="space-y-6">
                    <!-- Prix et Stock -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-600">
                                    {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                </div>
                                @if($product->stock_quantity > 0)
                                    <div class="text-sm text-green-600">
                                        ✓ {{ $product->stock_quantity }} en stock
                                    </div>
                                @else
                                    <div class="text-sm text-red-600">
                                        ❌ Rupture de stock
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        @if($product->description)
                            <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                        @endif
                    </div>
                    
                    <!-- Badges -->
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                            {{ ucfirst($product->type) }}
                        </span>
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">
                            {{ $product->duration_value }} {{ $product->duration_unit }}
                        </span>
                        @if($product->is_active)
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                ✓ Actif
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-full">
                                ❌ Inactif
                            </span>
                        @endif
                    </div>
                    
                    <!-- Informations Techniques -->
                    <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                        <h3 class="font-semibold text-gray-900">Informations</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Type:</span>
                                <span class="font-medium ml-2">{{ ucfirst($product->type) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Durée:</span>
                                <span class="font-medium ml-2">{{ $product->duration_value }} {{ $product->duration_unit }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Stock:</span>
                                <span class="font-medium ml-2">{{ $product->stock_quantity }} unités</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Photos:</span>
                                <span class="font-medium ml-2">{{ $product->photos->count() }} image(s)</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex space-x-3">
                        @can('update', $product)
                            <a href="{{ route('products.edit', $product) }}" 
                               class="flex-1 bg-blue-600 text-white text-center py-3 rounded-lg hover:bg-blue-700 transition font-medium">
                                ✏️ Modifier
                            </a>
                        @endcan
                        
                        <a href="{{ route('products.index') }}" 
                           class="flex-1 bg-gray-100 text-gray-700 text-center py-3 rounded-lg hover:bg-gray-200 transition font-medium">
                            📋 Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Produits Similaires -->
        @if($similarProducts->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Produits similaires</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($similarProducts as $similar)
                        <div class="group">
                            <a href="{{ route('products.show', $similar) }}" class="block">
                                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden mb-3">
                                    @php
                                        $primaryPhoto = $similar->photos->where('is_primary', true)->first() ?? $similar->photos->first();
                                    @endphp
                                    @if($primaryPhoto)
                                        <img src="{{ asset('storage/' . $primaryPhoto->path) }}" 
                                             alt="{{ $similar->name }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-blue-100 to-yellow-100 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <h4 class="font-medium text-gray-900 group-hover:text-blue-600 transition">{{ $similar->name }}</h4>
                                <p class="text-blue-600 font-semibold">{{ number_format($similar->price, 0, ',', ' ') }} FCFA</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Lightbox pour Images -->
    <div x-data="{ showLightbox: false, lightboxImage: '' }" x-show="showLightbox" x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-90"
         @click="showLightbox = false"
         @keydown.escape.window="showLightbox = false">
        <div class="relative max-w-4xl max-h-full p-4">
            <img x-bind:src="lightboxImage" alt="Image agrandie" class="max-w-full max-h-full object-contain">
            <button @click="showLightbox = false" 
                    class="absolute top-4 right-4 text-white hover:text-gray-300 text-2xl">
                ✕
            </button>
        </div>
    </div>

    <!-- Script pour Lightbox -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter click handlers pour ouvrir lightbox
            document.querySelectorAll('[data-lightbox]').forEach(img => {
                img.addEventListener('click', function() {
                    Alpine.evaluate(document.body, '$dispatch("open-lightbox", { src: "' + this.src + '" })');
                });
            });
        });
    </script>
</x-app-layout>
