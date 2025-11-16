<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardApiController extends Controller
{
    protected $dashboardController;

    public function __construct()
    {
        $this->middleware('auth');
        $this->dashboardController = new DashboardController();
    }

    /**
     * Charger les données des graphiques de manière asynchrone
     */
    public function getChartsData()
    {
        $user = auth()->user();
        
        if (!$user->hasRole('super_admin')) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Utiliser la réflexion pour accéder aux méthodes privées
        $reflection = new \ReflectionClass($this->dashboardController);
        
        // Données pour les graphiques avec cache
        $paymentsLast30Days = Cache::remember('dashboard.payments_30days', 300, function() use ($reflection) {
            $method = $reflection->getMethod('getPaymentsLast30Days');
            $method->setAccessible(true);
            return $method->invoke($this->dashboardController);
        });
        
        $amountsLast30Days = Cache::remember('dashboard.amounts_30days', 300, function() use ($reflection) {
            $method = $reflection->getMethod('getAmountsLast30Days');
            $method->setAccessible(true);
            return $method->invoke($this->dashboardController);
        });
        
        $monthlyStats = Cache::remember('dashboard.monthly_stats', 300, function() use ($reflection) {
            $method = $reflection->getMethod('getMonthlyStats');
            $method->setAccessible(true);
            return $method->invoke($this->dashboardController);
        });
        
        $agentsPerformance = Cache::remember('dashboard.agents_performance', 300, function() use ($reflection) {
            $method = $reflection->getMethod('getAgentsPerformance');
            $method->setAccessible(true);
            return $method->invoke($this->dashboardController);
        });

        return response()->json([
            'payments_chart_data' => $paymentsLast30Days,
            'amounts_chart_data' => $amountsLast30Days,
            'monthly_stats' => $monthlyStats,
            'agents_performance' => $agentsPerformance,
        ]);
    }

    /**
     * Vider le cache du dashboard
     */
    public function clearCache()
    {
        $user = auth()->user();
        
        if (!$user->hasRole('super_admin')) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        Cache::forget('dashboard.payments_30days');
        Cache::forget('dashboard.amounts_30days');
        Cache::forget('dashboard.monthly_stats');
        Cache::forget('dashboard.agents_performance');

        return response()->json(['message' => 'Cache vidé avec succès']);
    }
}
