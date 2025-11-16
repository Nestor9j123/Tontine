<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\SystemSetting;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Partager les variables de thème avec toutes les vues
        View::composer('*', function ($view) {
            $themeVars = $this->getThemeVariables();
            $view->with('themeVars', $themeVars);
        });
    }

    /**
     * Récupérer les variables de thème depuis la base de données
     */
    private function getThemeVariables()
    {
        try {
            return [
                'company_name' => SystemSetting::get('company_name', 'Tontine App'),
                'primary_color' => SystemSetting::get('primary_color', '#3B82F6'),
                'secondary_color' => SystemSetting::get('secondary_color', '#EAB308'),
                'theme_mode' => SystemSetting::get('theme_mode', 'light'),
                'low_stock_threshold' => SystemSetting::get('low_stock_threshold', 10),
            ];
        } catch (\Exception $e) {
            // En cas d'erreur (migration pas encore exécutée), retourner les valeurs par défaut
            return [
                'company_name' => 'Tontine App',
                'primary_color' => '#3B82F6',
                'secondary_color' => '#EAB308',
                'theme_mode' => 'light',
                'low_stock_threshold' => 10,
            ];
        }
    }
}
