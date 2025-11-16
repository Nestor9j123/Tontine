<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;

class SystemSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super_admin');
    }

    public function index()
    {
        // Valeurs par défaut pour éviter les erreurs
        $settings = [
            'company_name' => 'Tontine App',
            'company_email' => '',
            'company_phone' => '',
            'company_address' => '',
            'low_stock_threshold' => 10,
            'primary_color' => '#3B82F6',
            'secondary_color' => '#10B981',
            'accent_color' => '#F59E0B',
            'danger_color' => '#EF4444',
            'background_color' => '#FFFFFF',
            'surface_color' => '#F9FAFB',
            'text_color' => '#111827',
            'dark_primary_color' => '#2563EB',
            'dark_secondary_color' => '#059669',
            'dark_accent_color' => '#D97706',
            'dark_danger_color' => '#DC2626',
            'dark_background_color' => '#000000',
            'dark_surface_color' => '#1F2937',
            'dark_text_color' => '#F9FAFB',
            'theme_mode' => 'light',
            'theme_preset' => 'default',
            'enable_custom_colors' => false,
            'border_radius' => 8,
            'font_size' => 'medium',
            'sidebar_style' => 'expanded',
            'enable_animations' => true,
            'enable_shadows' => true,
            'enable_gradients' => true,
            'animation_speed' => 'normal',
            'high_contrast' => false,
            'reduced_motion' => false,
        ];

        // Fusionner avec les valeurs réellement stockées en base
        // (priorité aux valeurs de la base sur les valeurs par défaut)
        $storedSettings = SystemSetting::all_settings();
        foreach ($storedSettings as $key => $value) {
            $settings[$key] = $value;
        }
        
        // Préparer les couleurs de thème par défaut
        $themePresets = [
            'default' => [
                'name' => 'Défaut',
                'primary' => '#3B82F6',
                'secondary' => '#10B981',
                'accent' => '#F59E0B',
                'danger' => '#EF4444',
                'dark_primary' => '#2563EB',
                'dark_secondary' => '#059669',
                'dark_accent' => '#D97706',
                'dark_danger' => '#DC2626',
            ],
            'ocean' => [
                'name' => 'Océan',
                'primary' => '#0891B2',
                'secondary' => '#0E7490',
                'accent' => '#06B6D4',
                'danger' => '#083344',
                'dark_primary' => '#0C4A6E',
                'dark_secondary' => '#075985',
                'dark_accent' => '#0284C7',
                'dark_danger' => '#164E63',
            ],
            'forest' => [
                'name' => 'Forêt',
                'primary' => '#059669',
                'secondary' => '#047857',
                'accent' => '#10B981',
                'danger' => '#B91C1C',
                'dark_primary' => '#047857',
                'dark_secondary' => '#065F46',
                'dark_accent' => '#059669',
                'dark_danger' => '#991B1B',
            ],
            'sunset' => [
                'name' => 'Coucher de soleil',
                'primary' => '#F97316',
                'secondary' => '#EA580C',
                'accent' => '#FB923C',
                'danger' => '#DC2626',
                'dark_primary' => '#EA580C',
                'dark_secondary' => '#C2410C',
                'dark_accent' => '#F97316',
                'dark_danger' => '#B91C1C',
            ],
            'royal' => [
                'name' => 'Royal',
                'primary' => '#7C3AED',
                'secondary' => '#6D28D9',
                'accent' => '#8B5CF6',
                'danger' => '#BE123C',
                'dark_primary' => '#6D28D9',
                'dark_secondary' => '#5B21B6',
                'dark_accent' => '#7C3AED',
                'dark_danger' => '#9F1239',
            ],
        ];
        
        // Debug temporaire
        if (request()->has('debug')) {
            dd([
                'settings_count' => count($settings),
                'themePresets_count' => count($themePresets),
                'themePresets_keys' => array_keys($themePresets),
                'first_preset' => $themePresets['default'] ?? null
            ]);
        }
        
        return view('system-settings.index', compact('settings', 'themePresets'));
    }

    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'company_email' => 'nullable|email|max:255',
                'company_phone' => 'nullable|string|max:20',
                'company_address' => 'nullable|string|max:500',
                'low_stock_threshold' => 'required|integer|min:1|max:100',
                
                // Couleurs mode light
                'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'danger_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'background_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'surface_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                
                // Couleurs mode dark
                'dark_primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'dark_secondary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'dark_accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'dark_danger_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'dark_background_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'dark_surface_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'dark_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                
                // Paramètres du thème
                'theme_mode' => 'required|in:light,dark,auto',
                'theme_preset' => 'nullable|in:default,ocean,forest,sunset,royal',
                'enable_custom_colors' => 'boolean',
                'border_radius' => 'required|integer|min:0|max:20',
                'font_size' => 'required|in:small,medium,large',
                'sidebar_style' => 'required|in:expanded,collapsed,compact',
                
                // Paramètres avancés
                'enable_animations' => 'boolean',
                'enable_shadows' => 'boolean',
                'enable_gradients' => 'boolean',
                'animation_speed' => 'required|in:slow,normal,fast',
            ]);

            foreach ($validated as $key => $value) {
                $type = in_array($key, ['low_stock_threshold', 'border_radius']) ? 'integer' : 'string';
                SystemSetting::set($key, $value, $type);
            }

            return back()->with('success', 'Paramètres système et thèmes mis à jour avec succès !');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
    }
    
    public function applyPreset(Request $request)
    {
        $preset = $request->input('preset');
        $themePresets = [
            'default' => [
                'primary_color' => '#3B82F6',
                'secondary_color' => '#10B981',
                'accent_color' => '#F59E0B',
                'danger_color' => '#EF4444',
                'dark_primary_color' => '#2563EB',
                'dark_secondary_color' => '#059669',
                'dark_accent_color' => '#D97706',
                'dark_danger_color' => '#DC2626',
            ],
            'ocean' => [
                'primary_color' => '#0891B2',
                'secondary_color' => '#0E7490',
                'accent_color' => '#06B6D4',
                'danger_color' => '#083344',
                'dark_primary_color' => '#0C4A6E',
                'dark_secondary_color' => '#075985',
                'dark_accent_color' => '#0284C7',
                'dark_danger_color' => '#164E63',
            ],
            'forest' => [
                'primary_color' => '#059669',
                'secondary_color' => '#047857',
                'accent_color' => '#10B981',
                'danger_color' => '#B91C1C',
                'dark_primary_color' => '#047857',
                'dark_secondary_color' => '#065F46',
                'dark_accent_color' => '#059669',
                'dark_danger_color' => '#991B1B',
            ],
            'sunset' => [
                'primary_color' => '#F97316',
                'secondary_color' => '#EA580C',
                'accent_color' => '#FB923C',
                'danger_color' => '#DC2626',
                'dark_primary_color' => '#EA580C',
                'dark_secondary_color' => '#C2410C',
                'dark_accent_color' => '#F97316',
                'dark_danger_color' => '#B91C1C',
            ],
            'royal' => [
                'primary_color' => '#7C3AED',
                'secondary_color' => '#6D28D9',
                'accent_color' => '#8B5CF6',
                'danger_color' => '#BE123C',
                'dark_primary_color' => '#6D28D9',
                'dark_secondary_color' => '#5B21B6',
                'dark_accent_color' => '#7C3AED',
                'dark_danger_color' => '#9F1239',
            ],
        ];
        
        if (isset($themePresets[$preset])) {
            foreach ($themePresets[$preset] as $key => $value) {
                SystemSetting::set($key, $value, 'string');
            }
            SystemSetting::set('theme_preset', $preset, 'string');
            
            return response()->json([
                'success' => true,
                'message' => 'Thème appliqué avec succès !',
                'colors' => $themePresets[$preset]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Thème non trouvé'
        ], 404);
    }
    
    public function resetTheme()
    {
        $defaultColors = [
            'primary_color' => '#3B82F6',
            'secondary_color' => '#10B981',
            'accent_color' => '#F59E0B',
            'danger_color' => '#EF4444',
            'dark_primary_color' => '#2563EB',
            'dark_secondary_color' => '#059669',
            'dark_accent_color' => '#D97706',
            'dark_danger_color' => '#DC2626',
            'theme_mode' => 'light',
            'theme_preset' => 'default',
            'enable_custom_colors' => false,
            'border_radius' => 8,
            'font_size' => 'medium',
            'sidebar_style' => 'expanded',
            'enable_animations' => true,
            'enable_shadows' => true,
            'enable_gradients' => true,
            'animation_speed' => 'normal',
        ];
        
        foreach ($defaultColors as $key => $value) {
            $type = in_array($key, ['border_radius']) ? 'integer' : 'string';
            SystemSetting::set($key, $value, $type);
        }
        
        return back()->with('success', 'Thème réinitialisé avec succès !');
    }
}
