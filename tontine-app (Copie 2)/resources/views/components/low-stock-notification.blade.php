@if(auth()->user()->hasRole(['super_admin', 'secretary']))
@php
    $threshold = \App\Models\SystemSetting::get('low_stock_threshold', 10);
    $lowStockProducts = \App\Models\Product::where('is_active', true)
        ->where(function($query) use ($threshold) {
            $query->where('stock_quantity', '<=', $threshold)
                  ->where('stock_quantity', '>', 0);
        })
        ->orWhere('stock_quantity', 0)
        ->count();
@endphp

@if($lowStockProducts > 0)
<div id="low-stock-notification" class="fixed bottom-4 right-4 z-40">
    <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-4 py-3 rounded-lg shadow-lg animate-pulse-slow relative">
        <!-- Bouton fermer -->
        <button onclick="document.getElementById('low-stock-notification').style.display='none'" 
                class="absolute -top-2 -right-2 bg-red-600 hover:bg-red-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs transition-colors">
            ×
        </button>
        
        <div class="flex items-center space-x-3">
            <div class="animate-bounce">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-sm">Stock Faible!</p>
                <p class="text-xs opacity-90">{{ $lowStockProducts }} produit{{ $lowStockProducts > 1 ? 's' : '' }} en rupture/stock faible</p>
            </div>
            <a href="{{ route('products.low-stock') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-3 py-1 rounded text-xs font-medium transition-colors">
                Voir
            </a>
        </div>
    </div>
</div>

<style>
@keyframes pulse-slow {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.85;
        transform: scale(1.01);
    }
}

.animate-pulse-slow {
    animation: pulse-slow 4s ease-in-out infinite;
}

@keyframes gentle-glow {
    0%, 100% {
        box-shadow: 0 0 5px rgba(239, 68, 68, 0.3);
    }
    50% {
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.6);
    }
}

.animate-gentle-glow {
    animation: gentle-glow 3s ease-in-out infinite;
}

@keyframes subtle-shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-1px); }
    75% { transform: translateX(1px); }
}

.animate-subtle-shake {
    animation: subtle-shake 2s ease-in-out infinite;
}

@keyframes fade-in-out {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}

.animate-fade-in-out {
    animation: fade-in-out 2.5s ease-in-out infinite;
}
</style>
@endif
@endif
