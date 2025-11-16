<div x-data="{ open: false }" class="relative">
    <!-- Bouton du sélecteur -->
    <button @click="open = !open" 
            class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
        
        <span class="text-lg">🎨</span>
        <span>Thème</span>
        
        <svg class="w-4 h-4 transition-transform duration-200" 
             :class="{ 'rotate-180': open }" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    
    <!-- Menu déroulant -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.outside="open = false"
         class="absolute right-0 z-50 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg">
        
        <div class="py-1">
            <button class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <span class="mr-3">🎨</span>
                <span>Défaut</span>
            </button>
            <button class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <span class="mr-3">🌙</span>
                <span>Sombre</span>
            </button>
            <button class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <span class="mr-3">💼</span>
                <span>Corporate</span>
            </button>
        </div>
    </div>
</div>
