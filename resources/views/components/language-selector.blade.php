@php
    $currentLocale = app()->getLocale();
    $supportedLocales = ['en' => 'English', 'fr' => 'Français'];
@endphp

<div x-data="{ 
    locale: '{{ $currentLocale }}',
    open: false,
    changeLanguage(lang) {
        window.location.href = '/' + lang + window.location.pathname.substring(3);
    }
}" class="relative">
    
    <!-- Bouton du sélecteur -->
    <button @click="open = !open" 
            class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        
        <!-- Icône du drapeau -->
        <span class="text-lg">
            @switch($currentLocale)
                @case('en')
                    🇺🇸
                    @break
                @case('fr')
                    🇫🇷
                    @break
                @default
                    🌐
            @endswitch
        </span>
        
        <!-- Nom de la langue -->
        <span>{{ $supportedLocales[$currentLocale] ?? 'Language' }}</span>
        
        <!-- Flèche -->
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
            @foreach($supportedLocales as $locale => $name)
                <button @click="changeLanguage('{{ $locale }}')"
                        class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 {{ $currentLocale === $locale ? 'bg-blue-50 text-blue-700' : '' }}">
                    
                    <!-- Icône du drapeau -->
                    <span class="mr-3 text-lg">
                        @switch($locale)
                            @case('en')
                                🇺🇸
                                @break
                            @case('fr')
                                🇫🇷
                                @break
                            @default
                                🌐
                        @endswitch
                    </span>
                    
                    <!-- Nom de la langue -->
                    <span>{{ $name }}</span>
                    
                    <!-- Indicateur de sélection -->
                    @if($currentLocale === $locale)
                        <svg class="w-4 h-4 ml-auto text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>
