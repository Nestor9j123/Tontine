<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SystemSetting;

class ThemeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Récupérer les paramètres de thème
        $themeSettings = [
            'primary_color' => SystemSetting::get('primary_color', '#3B82F6'),
            'secondary_color' => SystemSetting::get('secondary_color', '#10B981'),
            'accent_color' => SystemSetting::get('accent_color', '#F59E0B'),
            'danger_color' => SystemSetting::get('danger_color', '#EF4444'),
            'background_color' => SystemSetting::get('background_color', '#FFFFFF'),
            'surface_color' => SystemSetting::get('surface_color', '#F9FAFB'),
            'text_color' => SystemSetting::get('text_color', '#111827'),
            'dark_primary_color' => SystemSetting::get('dark_primary_color', '#2563EB'),
            'dark_secondary_color' => SystemSetting::get('dark_secondary_color', '#059669'),
            'dark_accent_color' => SystemSetting::get('dark_accent_color', '#D97706'),
            'dark_danger_color' => SystemSetting::get('dark_danger_color', '#DC2626'),
            'dark_background_color' => SystemSetting::get('dark_background_color', '#000000'),
            'dark_surface_color' => SystemSetting::get('dark_surface_color', '#1F2937'),
            'dark_text_color' => SystemSetting::get('dark_text_color', '#F9FAFB'),
            'theme_mode' => SystemSetting::get('theme_mode', 'light'),
            'border_radius' => SystemSetting::get('border_radius', 8),
            'font_size' => SystemSetting::get('font_size', 'medium'),
            'enable_animations' => SystemSetting::get('enable_animations', '1') === '1',
            'enable_shadows' => SystemSetting::get('enable_shadows', '1') === '1',
            'enable_gradients' => SystemSetting::get('enable_gradients', '1') === '1',
            'animation_speed' => SystemSetting::get('animation_speed', 'normal'),
        ];

        // Partager les variables de thème avec toutes les vues
        view()->share('themeSettings', $themeSettings);

        // Générer le CSS personnalisé
        $css = $this->generateThemeCSS($themeSettings);
        view()->share('themeCSS', $css);

        return $next($request);
    }

    private function generateThemeCSS($settings)
    {
        $css = "<style>\n";
        $css .= ":root {\n";
        
        // Variables CSS pour le mode light
        $css .= "  --color-primary: {$settings['primary_color']};\n";
        $css .= "  --color-secondary: {$settings['secondary_color']};\n";
        $css .= "  --color-accent: {$settings['accent_color']};\n";
        $css .= "  --color-danger: {$settings['danger_color']};\n";
        $css .= "  --color-background: {$settings['background_color']};\n";
        $css .= "  --color-surface: {$settings['surface_color']};\n";
        $css .= "  --color-text: {$settings['text_color']};\n";
        
        // Variables CSS pour le mode dark
        $css .= "  --color-dark-primary: {$settings['dark_primary_color']};\n";
        $css .= "  --color-dark-secondary: {$settings['dark_secondary_color']};\n";
        $css .= "  --color-dark-accent: {$settings['dark_accent_color']};\n";
        $css .= "  --color-dark-danger: {$settings['dark_danger_color']};\n";
        $css .= "  --color-dark-background: {$settings['dark_background_color']};\n";
        $css .= "  --color-dark-surface: {$settings['dark_surface_color']};\n";
        $css .= "  --color-dark-text: {$settings['dark_text_color']};\n";
        
        // Variables de style
        $css .= "  --border-radius: {$settings['border_radius']}px;\n";
        $css .= "  --animation-speed: {$this->getAnimationSpeed($settings['animation_speed'])};\n";
        
        $css .= "}\n\n";
        
        // Styles pour le mode light
        $css .= "[data-theme='light'] {\n";
        $css .= "  --color-primary: {$settings['primary_color']};\n";
        $css .= "  --color-secondary: {$settings['secondary_color']};\n";
        $css .= "  --color-accent: {$settings['accent_color']};\n";
        $css .= "  --color-danger: {$settings['danger_color']};\n";
        $css .= "  --color-background: {$settings['background_color']};\n";
        $css .= "  --color-surface: {$settings['surface_color']};\n";
        $css .= "  --color-text: {$settings['text_color']};\n";
        $css .= "}\n\n";
        
        // Styles pour le mode dark
        $css .= "[data-theme='dark'] {\n";
        $css .= "  --color-primary: {$settings['dark_primary_color']};\n";
        $css .= "  --color-secondary: {$settings['dark_secondary_color']};\n";
        $css .= "  --color-accent: {$settings['dark_accent_color']};\n";
        $css .= "  --color-danger: {$settings['dark_danger_color']};\n";
        $css .= "  --color-background: {$settings['dark_background_color']};\n";
        $css .= "  --color-surface: {$settings['dark_surface_color']};\n";
        $css .= "  --color-text: {$settings['dark_text_color']};\n";
        $css .= "}\n\n";
        
        // Styles dynamiques
        $css .= ".bg-primary { background-color: var(--color-primary) !important; }\n";
        $css .= ".bg-secondary { background-color: var(--color-secondary) !important; }\n";
        $css .= ".bg-accent { background-color: var(--color-accent) !important; }\n";
        $css .= ".bg-danger { background-color: var(--color-danger) !important; }\n";
        $css .= ".text-primary { color: var(--color-primary) !important; }\n";
        $css .= ".text-secondary { color: var(--color-secondary) !important; }\n";
        $css .= ".text-accent { color: var(--color-accent) !important; }\n";
        $css .= ".text-danger { color: var(--color-danger) !important; }\n";
        $css .= ".border-primary { border-color: var(--color-primary) !important; }\n";
        $css .= ".border-secondary { border-color: var(--color-secondary) !important; }\n";
        $css .= ".border-accent { border-color: var(--color-accent) !important; }\n";
        $css .= ".border-danger { border-color: var(--color-danger) !important; }\n";
        
        // Styles pour les bordures arrondies
        $css .= ".rounded-theme { border-radius: var(--border-radius) !important; }\n";
        
        // Styles pour les animations
        if (!$settings['enable_animations']) {
            $css .= "* { transition: none !important; animation: none !important; }\n";
        }
        
        // Styles pour les ombres
        if (!$settings['enable_shadows']) {
            $css .= ".shadow, .shadow-sm, .shadow-md, .shadow-lg, .shadow-xl { box-shadow: none !important; }\n";
        }
        
        // Taille de police
        $fontSize = $this->getFontSize($settings['font_size']);
        $css .= "body { font-size: {$fontSize} !important; }\n";
        
        $css .= "</style>";
        
        return $css;
    }

    private function getAnimationSpeed($speed)
    {
        switch ($speed) {
            case 'slow': return '0.5s';
            case 'fast': return '0.1s';
            default: return '0.3s';
        }
    }

    private function getFontSize($size)
    {
        switch ($size) {
            case 'small': return '14px';
            case 'large': return '18px';
            default: return '16px';
        }
    }
}
