<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Tontine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // Données pour les graphiques des 30 derniers jours
        $paymentsLast30Days = $this->getPaymentsLast30Days();
        $amountsLast30Days = $this->getAmountsLast30Days();
        $monthlyStats = $this->getMonthlyStats();
        
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
            'agents_performance' => $this->getAgentsPerformance(),
        ];
    }

    private function getPaymentsLast30Days()
    {
        $data = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
            $data[] = Payment::whereDate('payment_date', $date)->count();
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    private function getAmountsLast30Days()
    {
        $validatedData = [];
        $pendingData = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('d/m');
            $validatedData[] = Payment::whereDate('payment_date', $date)
                ->where('status', 'validated')
                ->sum('amount');
            $pendingData[] = Payment::whereDate('payment_date', $date)
                ->where('status', 'pending')
                ->sum('amount');
        }
        
        return [
            'labels' => $labels,
            'validated' => $validatedData,
            'pending' => $pendingData
        ];
    }

    private function getMonthlyStats()
    {
        $months = [];
        $paymentsData = [];
        $amountsData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');
            
            $monthPayments = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->count();
            $paymentsData[] = $monthPayments;
            
            $monthAmount = Payment::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->where('status', 'validated')
                ->sum('amount');
            $amountsData[] = $monthAmount;
        }
        
        return [
            'labels' => $months,
            'payments' => $paymentsData,
            'amounts' => $amountsData
        ];
    }

    private function getAgentsPerformance()
    {
        $agents = User::role('agent')->get();
        $performance = [];
        
        foreach ($agents as $agent) {
            $totalAmount = Payment::where('collected_by', $agent->id)
                ->where('status', 'validated')
                ->sum('amount');
                
            $totalPayments = Payment::where('collected_by', $agent->id)->count();
            
            $performance[] = (object) [
                'name' => $agent->name,
                'total_amount' => $totalAmount,
                'total_payments' => $totalPayments
            ];
        }
        
        // Trier par montant décroissant et prendre les 10 premiers
        usort($performance, function($a, $b) {
            return $b->total_amount <=> $a->total_amount;
        });
        
        return array_slice($performance, 0, 10);
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
