@php
    // Fonctions helper pour les couleurs
    function hexToRgb($hex) {
        $hex = ltrim($hex, '#');
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }

    function rgbToHex($r, $g, $b) {
        return sprintf("#%02x%02x%02x", round($r), round($g), round($b));
    }

    function lightenColor($hex, $percent) {
        $rgb = hexToRgb($hex);
        
        $rgb['r'] = min(255, $rgb['r'] + (255 - $rgb['r']) * $percent);
        $rgb['g'] = min(255, $rgb['g'] + (255 - $rgb['g']) * $percent);
        $rgb['b'] = min(255, $rgb['b'] + (255 - $rgb['b']) * $percent);
        
        return rgbToHex($rgb['r'], $rgb['g'], $rgb['b']);
    }

    function darkenColor($hex, $percent) {
        $rgb = hexToRgb($hex);
        
        $rgb['r'] = max(0, $rgb['r'] * (1 - $percent));
        $rgb['g'] = max(0, $rgb['g'] * (1 - $percent));
        $rgb['b'] = max(0, $rgb['b'] * (1 - $percent));
        
        return rgbToHex($rgb['r'], $rgb['g'], $rgb['b']);
    }

    $primaryColor = $themeVars['primary_color'] ?? '#3B82F6';
    $secondaryColor = $themeVars['secondary_color'] ?? '#EAB308';
@endphp

{{-- Injection des variables CSS dynamiques --}}
<style>
:root {
    /* Variables de couleurs personnalisées */
    --primary-color: {{ $primaryColor }};
    --secondary-color: {{ $secondaryColor }};
    
    /* Variations de la couleur primaire */
    --primary-50: {{ lightenColor($primaryColor, 0.95) }};
    --primary-100: {{ lightenColor($primaryColor, 0.9) }};
    --primary-200: {{ lightenColor($primaryColor, 0.8) }};
    --primary-300: {{ lightenColor($primaryColor, 0.6) }};
    --primary-400: {{ lightenColor($primaryColor, 0.4) }};
    --primary-500: {{ $primaryColor }};
    --primary-600: {{ darkenColor($primaryColor, 0.1) }};
    --primary-700: {{ darkenColor($primaryColor, 0.2) }};
    --primary-800: {{ darkenColor($primaryColor, 0.3) }};
    --primary-900: {{ darkenColor($primaryColor, 0.4) }};
    
    /* Variations de la couleur secondaire */
    --secondary-50: {{ lightenColor($secondaryColor, 0.95) }};
    --secondary-100: {{ lightenColor($secondaryColor, 0.9) }};
    --secondary-200: {{ lightenColor($secondaryColor, 0.8) }};
    --secondary-300: {{ lightenColor($secondaryColor, 0.6) }};
    --secondary-400: {{ lightenColor($secondaryColor, 0.4) }};
    --secondary-500: {{ $secondaryColor }};
    --secondary-600: {{ darkenColor($secondaryColor, 0.1) }};
    --secondary-700: {{ darkenColor($secondaryColor, 0.2) }};
    --secondary-800: {{ darkenColor($secondaryColor, 0.3) }};
    --secondary-900: {{ darkenColor($secondaryColor, 0.4) }};
}

/* Application des couleurs aux éléments existants */
.bg-blue-600, .bg-gradient-to-r.from-blue-600 {
    background-color: var(--primary-500) !important;
}

.bg-blue-700, .hover\:bg-blue-700:hover, .bg-gradient-to-r.from-blue-600.to-yellow-500:hover {
    background-color: var(--primary-600) !important;
}

.text-blue-600 {
    color: var(--primary-500) !important;
}

.text-blue-800, .hover\:text-blue-800:hover {
    color: var(--primary-700) !important;
}

.border-blue-500, .focus\:border-blue-500:focus {
    border-color: var(--primary-500) !important;
}

.ring-blue-500, .focus\:ring-blue-500:focus {
    --tw-ring-color: var(--primary-500) !important;
}

/* Couleurs secondaires */
.bg-yellow-500, .bg-gradient-to-r.to-yellow-500 {
    background-color: var(--secondary-500) !important;
}

.bg-yellow-600, .hover\:bg-yellow-600:hover, .bg-gradient-to-r.to-yellow-500:hover {
    background-color: var(--secondary-600) !important;
}

.text-yellow-500 {
    color: var(--secondary-500) !important;
}

/* Gradients personnalisés */
.bg-gradient-to-r.from-blue-600.to-yellow-500 {
    background: linear-gradient(to right, var(--primary-500), var(--secondary-500)) !important;
}

.hover\:from-blue-700.hover\:to-yellow-600:hover {
    background: linear-gradient(to right, var(--primary-600), var(--secondary-600)) !important;
}

/* Boutons et éléments interactifs */
.btn-primary {
    background-color: var(--primary-500);
    border-color: var(--primary-500);
    color: white;
}

.btn-primary:hover {
    background-color: var(--primary-600);
    border-color: var(--primary-600);
}

.btn-secondary {
    background-color: var(--secondary-500);
    border-color: var(--secondary-500);
    color: white;
}

.btn-secondary:hover {
    background-color: var(--secondary-600);
    border-color: var(--secondary-600);
}

/* Mode sombre */
@if(($themeVars['theme_mode'] ?? 'light') === 'dark')
body {
    background-color: #1a1a1a !important;
    color: #e5e5e5 !important;
}

.bg-white {
    background-color: #2d2d2d !important;
    color: #e5e5e5 !important;
}

.text-gray-900 {
    color: #e5e5e5 !important;
}

.text-gray-800 {
    color: #d1d1d1 !important;
}

.text-gray-700 {
    color: #b5b5b5 !important;
}

.text-gray-600 {
    color: #9a9a9a !important;
}

.text-gray-500 {
    color: #7e7e7e !important;
}

.border-gray-100, .border-gray-200, .border-gray-300 {
    border-color: #404040 !important;
}

.bg-gray-50, .bg-gray-100 {
    background-color: #2a2a2a !important;
}

.hover\:bg-gray-50:hover {
    background-color: #353535 !important;
}
@endif
</style>
