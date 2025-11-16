<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Tontine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Statistiques selon le rôle
        if ($user->hasRole('super_admin')) {
            $stats = $this->getSuperAdminStats();
        } elseif ($user->hasRole('secretary')) {
            $stats = $this->getSecretaryStats();
        } else {
            $stats = $this->getAgentStats($user->id);
        }

        return view('dashboard', compact('stats'));
    }

    private function getSuperAdminStats()
    {
        // Mise en cache des données lourdes pour 5 minutes
        $paymentsLast30Days = Cache::remember('dashboard.payments_30days', 300, function() {
            return $this->getPaymentsLast30Days();
        });
        
        $amountsLast30Days = Cache::remember('dashboard.amounts_30days', 300, function() {
            return $this->getAmountsLast30Days();
        });
        
        $monthlyStats = Cache::remember('dashboard.monthly_stats', 300, function() {
            return $this->getMonthlyStats();
        });
        
        $agentsPerformance = Cache::remember('dashboard.agents_performance', 300, function() {
            return $this->getAgentsPerformance();
        });
        
        return [
            'total_clients' => Client::count(),
            'active_clients' => Client::active()->count(),
            'total_agents' => User::role('agent')->count(),
            'total_secretaries' => User::role('secretary')->count(),
            'total_products' => Product::count(),
            'active_products' => Product::active()->count(),
            'total_tontines' => Tontine::count(),
            'active_tontines' => Tontine::active()->count(),
            'completed_tontines' => Tontine::completed()->count(),
            'total_payments' => Payment::count(),
            'pending_payments' => Payment::pending()->count(),
            'validated_payments' => Payment::validated()->count(),
            'today_payments' => Payment::today()->count(),
            'total_amount_collected' => Payment::validated()->sum('amount'),
            'pending_amount' => Payment::pending()->sum('amount'),
            'recent_payments' => Payment::with(['client', 'collector', 'tontine.product'])
                ->latest()
                ->take(10)
                ->get(),
            'recent_tontines' => Tontine::with(['client', 'product', 'agent'])
                ->latest()
                ->take(10)
                ->get(),
            'top_agents' => User::role('agent')
                ->withCount('payments')
                ->orderBy('payments_count', 'desc')
                ->take(5)
                ->get(),
            // Données pour les graphiques
            'payments_chart_data' => $paymentsLast30Days,
            'amounts_chart_data' => $amountsLast30Days,
            'monthly_stats' => $monthlyStats,
            'agents_performance' => $agentsPerformance,
        ];
    }

    private function getPaymentsLast30Days()
    {
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();
        
        // Une seule requête optimisée avec GROUP BY
        $payments = Payment::selectRaw('DATE(payment_date) as date, COUNT(*) as count')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();
        
        $data = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $data[] = $payments[$dateKey] ?? 0;
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    private function getAmountsLast30Days()
    {
        $startDate = now()->subDays(29)->startOfDay();
        $endDate = now()->endOfDay();
        
        // Deux requêtes optimisées avec GROUP BY
        $validatedAmounts = Payment::selectRaw('DATE(payment_date) as date, SUM(amount) as total')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'validated')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();
            
        $pendingAmounts = Payment::selectRaw('DATE(payment_date) as date, SUM(amount) as total')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'pending')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();
        
        $validatedData = [];
        $pendingData = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateKey = $date->format('Y-m-d');
            $labels[] = $date->format('d/m');
            $validatedData[] = $validatedAmounts[$dateKey] ?? 0;
            $pendingData[] = $pendingAmounts[$dateKey] ?? 0;
        }
        
        return [
            'labels' => $labels,
            'validated' => $validatedData,
            'pending' => $pendingData
        ];
    }

    private function getMonthlyStats()
    {
        $startDate = now()->subMonths(5)->startOfMonth();
        $endDate = now()->endOfMonth();
        
        // Requêtes optimisées avec GROUP BY pour les 6 derniers mois (compatible PostgreSQL)
        $monthlyPayments = Payment::selectRaw("TO_CHAR(payment_date, 'YYYY-MM') as month_key, COUNT(*) as count")
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('count', 'month_key')
            ->toArray();
            
        $monthlyAmounts = Payment::selectRaw("TO_CHAR(payment_date, 'YYYY-MM') as month_key, SUM(amount) as total")
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'validated')
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('total', 'month_key')
            ->toArray();
        
        $months = [];
        $paymentsData = [];
        $amountsData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            $monthKey = $date->format('Y-m');
            
            $paymentsData[] = $monthlyPayments[$monthKey] ?? 0;
            $amountsData[] = $monthlyAmounts[$monthKey] ?? 0;
        }
        
        return [
            'labels' => $months,
            'payments' => $paymentsData,
            'amounts' => $amountsData
        ];
    }

    private function getAgentsPerformance()
    {
        // Une seule requête optimisée avec JOIN et GROUP BY
        $performance = User::role('agent')
            ->leftJoin('payments', 'users.id', '=', 'payments.collected_by')
            ->selectRaw("
                users.id,
                users.name,
                COALESCE(SUM(CASE WHEN payments.status = 'validated' THEN payments.amount ELSE 0 END), 0) as total_amount,
                COALESCE(COUNT(payments.id), 0) as total_payments
            ")
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_amount', 'desc')
            ->take(10)
            ->get()
            ->map(function($agent) {
                return (object) [
                    'name' => $agent->name,
                    'total_amount' => $agent->total_amount,
                    'total_payments' => $agent->total_payments
                ];
            });
        
        return $performance->toArray();
    }

    private function getSecretaryStats()
    {
        return [
            'total_clients' => Client::count(),
            'active_clients' => Client::active()->count(),
            'total_tontines' => Tontine::count(),
            'active_tontines' => Tontine::active()->count(),
            'pending_payments' => Payment::pending()->count(),
            'validated_payments' => Payment::validated()->count(),
            'today_payments' => Payment::today()->count(),
            'total_amount_collected' => Payment::validated()->sum('amount'),
            'pending_amount' => Payment::pending()->sum('amount'),
            'pending_validations' => Payment::with(['client', 'collector', 'tontine.product'])
                ->pending()
                ->latest()
                ->take(10)
                ->get(),
            'recent_payments' => Payment::with(['client', 'collector', 'tontine.product'])
                ->validated()
                ->latest()
                ->take(10)
                ->get(),
        ];
    }

    private function getAgentStats($agentId)
    {
        // Récupérer les stats de performance et classement
        $performanceController = new AgentPerformanceController();
        $ranking = $performanceController->getMyRanking($agentId);
        
        return array_merge([
            'ranking' => $ranking,
            'my_clients' => Client::byAgent($agentId)->count(),
            'active_clients' => Client::byAgent($agentId)->active()->count(),
            'my_tontines' => Tontine::byAgent($agentId)->count(),
            'active_tontines' => Tontine::byAgent($agentId)->active()->count(),
            'my_payments' => Payment::byAgent($agentId)->count(),
            'pending_payments' => Payment::byAgent($agentId)->pending()->count(),
            'validated_payments' => Payment::byAgent($agentId)->validated()->count(),
            'today_payments' => Payment::byAgent($agentId)->today()->count(),
            'total_collected' => Payment::byAgent($agentId)->validated()->sum('amount'),
            'pending_amount' => Payment::byAgent($agentId)->pending()->sum('amount'),
            'recent_clients' => Client::with('tontines')
                ->byAgent($agentId)
                ->latest()
                ->take(5)
                ->get(),
            'recent_payments' => Payment::with(['client', 'tontine.product'])
                ->byAgent($agentId)
                ->latest()
                ->take(10)
                ->get(),
            'active_tontines_list' => Tontine::with(['client', 'product'])
                ->byAgent($agentId)
                ->active()
                ->latest()
                ->take(10)
                ->get(),
        ], $ranking ? [] : []);
    }
}
