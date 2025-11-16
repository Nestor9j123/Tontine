<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ThemeController extends Controller
{
    public function index()
    {
        $themes = $this->getAvailableThemes();
        $currentTheme = $this->getCurrentTheme();
        
        return view('themes.index', compact('themes', 'currentTheme'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:' . implode(',', array_keys($this->getAvailableThemes())),
            'custom_colors' => 'array',
            'custom_colors.*' => 'string|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        $user = Auth::user();
        
        // Sauvegarder les préférences de thème
        $preferences = $user->theme_preferences ?? [];
        
        $preferences['theme'] = $request->theme;
        
        if ($request->theme === 'custom' && $request->has('custom_colors')) {
            $preferences['custom_colors'] = $request->custom_colors;
        }
        
        $user->update(['theme_preferences' => $preferences]);

        return response()->json([
            'success' => true,
            'message' => 'Thème mis à jour avec succès',
            'theme' => $preferences
        ]);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:' . implode(',', array_keys($this->getAvailableThemes())),
            'custom_colors' => 'array'
        ]);

        $theme = $request->theme;
        $customColors = $request->get('custom_colors', []);
        
        // Générer le CSS dynamique pour l'aperçu
        $css = $this->generateThemeCSS($theme, $customColors);
        
        return response()->json([
            'css' => $css,
            'theme' => $theme,
            'custom_colors' => $customColors
        ]);
    }

    public function reset()
    {
        $user = Auth::user();
        $user->update(['theme_preferences' => null]);
        
        return response()->json([
            'success' => true,
            'message' => 'Thème réinitialisé avec succès'
        ]);
    }

    private function getAvailableThemes()
    {
        return [
            'default' => [
                'name' => 'Défaut',
                'description' => 'Thème bleu classique',
                'colors' => [
                    'primary' => '#2563eb',
                    'secondary' => '#64748b',
                    'accent' => '#3b82f6',
                    'background' => '#ffffff',
                    'surface' => '#f8fafc',
                    'text' => '#1e293b',
                    'text_secondary' => '#64748b'
                ]
            ],
            'dark' => [
                'name' => 'Sombre',
                'description' => 'Thème sombre élégant',
                'colors' => [
                    'primary' => '#3b82f6',
                    'secondary' => '#94a3b8',
                    'accent' => '#60a5fa',
                    'background' => '#0f172a',
                    'surface' => '#1e293b',
                    'text' => '#f1f5f9',
                    'text_secondary' => '#94a3b8'
                ]
            ],
            'corporate' => [
                'name' => 'Corporate',
                'description' => 'Thème professionnel',
                'colors' => [
                    'primary' => '#1e40af',
                    'secondary' => '#475569',
                    'accent' => '#2563eb',
                    'background' => '#ffffff',
                    'surface' => '#f1f5f9',
                    'text' => '#0f172a',
                    'text_secondary' => '#475569'
                ]
            ],
            'modern' => [
                'name' => 'Moderne',
                'description' => 'Thème moderne et vibrant',
                'colors' => [
                    'primary' => '#7c3aed',
                    'secondary' => '#6b7280',
                    'accent' => '#8b5cf6',
                    'background' => '#ffffff',
                    'surface' => '#faf5ff',
                    'text' => '#1f2937',
                    'text_secondary' => '#6b7280'
                ]
            ],
            'nature' => [
                'name' => 'Nature',
                'description' => 'Thème vert apaisant',
                'colors' => [
                    'primary' => '#059669',
                    'secondary' => '#6b7280',
                    'accent' => '#10b981',
                    'background' => '#ffffff',
                    'surface' => '#f0fdf4',
                    'text' => '#1f2937',
                    'text_secondary' => '#6b7280'
                ]
            ],
            'sunset' => [
                'name' => 'Coucher de soleil',
                'description' => 'Thème orange chaleureux',
                'colors' => [
                    'primary' => '#ea580c',
                    'secondary' => '#6b7280',
                    'accent' => '#f97316',
                    'background' => '#fff7ed',
                    'surface' => '#fed7aa',
                    'text' => '#1f2937',
                    'text_secondary' => '#6b7280'
                ]
            ],
            'high_contrast' => [
                'name' => 'Contraste élevé',
                'description' => 'Thème accessible',
                'colors' => [
                    'primary' => '#000000',
                    'secondary' => '#ffffff',
                    'accent' => '#ffffff',
                    'background' => '#ffffff',
                    'surface' => '#f3f4f6',
                    'text' => '#000000',
                    'text_secondary' => '#374151'
                ]
            ],
            'custom' => [
                'name' => 'Personnalisé',
                'description' => 'Créez votre propre thème',
                'colors' => []
            ]
        ];
    }

    private function getCurrentTheme()
    {
        $user = Auth::user();
        return $user->theme_preferences ?? ['theme' => 'default'];
    }

    private function generateThemeCSS($themeName, $customColors = [])
    {
        $themes = $this->getAvailableThemes();
        $theme = $themes[$themeName] ?? $themes['default'];
        
        if ($themeName === 'custom' && !empty($customColors)) {
            $colors = $customColors;
        } else {
            $colors = $theme['colors'] ?? [];
        }
        
        $css = ":root {\n";
        
        foreach ($colors as $key => $value) {
            $css .= "    --theme-{$key}: {$value};\n";
        }
        
        // Ajouter des variables CSS dérivées
        if (isset($colors['primary'])) {
            $css .= "    --theme-primary-hover: " . $this->adjustColor($colors['primary'], -20) . ";\n";
            $css .= "    --theme-primary-light: " . $this->adjustColor($colors['primary'], 80) . ";\n";
        }
        
        if (isset($colors['background'])) {
            $css .= "    --theme-border: " . $this->adjustColor($colors['background'], -10) . ";\n";
        }
        
        $css .= "}\n";
        
        return $css;
    }

    private function adjustColor($hex, $percent)
    {
        // Convertir hex en RGB
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Ajuster la luminosité
        if ($percent > 0) {
            // Éclaircir
            $r = min(255, $r + (255 - $r) * $percent / 100);
            $g = min(255, $g + (255 - $g) * $percent / 100);
            $b = min(255, $b + (255 - $b) * $percent / 100);
        } else {
            // Assombrir
            $r = max(0, $r + $r * $percent / 100);
            $g = max(0, $g + $g * $percent / 100);
            $b = max(0, $b + $b * $percent / 100);
        }
        
        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) 
                   . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) 
                   . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
}
