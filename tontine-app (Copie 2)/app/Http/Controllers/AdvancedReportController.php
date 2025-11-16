<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Tontine;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TontineReportExport;

class AdvancedReportController extends Controller
{
    public function dashboard()
    {
        return view('reports.advanced.dashboard');
    }

    public function getKpiData(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        
        // KPIs principaux
        $kpis = [
            'total_revenue' => Payment::where('created_at', '>=', $startDate)
                ->where('status', 'paid')->sum('amount'),
            'total_clients' => Client::where('created_at', '>=', $startDate)->count(),
            'active_tontines' => Tontine::where('status', 'active')->count(),
            'payment_rate' => $this->calculatePaymentRate($startDate),
            'growth_rate' => $this->calculateGrowthRate($startDate),
        ];

        // Données pour graphiques
        $chartData = [
            'revenue_chart' => $this->getRevenueChartData($startDate, $period),
            'payment_distribution' => $this->getPaymentDistribution($startDate),
            'tontine_performance' => $this->getTontinePerformance($startDate),
            'agent_ranking' => $this->getAgentRanking($startDate),
        ];

        return response()->json([
            'kpis' => $kpis,
            'charts' => $chartData,
            'period' => $period,
        ]);
    }

    private function getStartDate($period)
    {
        switch ($period) {
            case 'week':
                return Carbon::now()->subWeek();
            case 'month':
                return Carbon::now()->subMonth();
            case 'quarter':
                return Carbon::now()->subQuarter();
            case 'year':
                return Carbon::now()->subYear();
            default:
                return Carbon::now()->subMonth();
        }
    }

    private function calculatePaymentRate($startDate)
    {
        $totalExpected = Payment::where('created_at', '>=', $startDate)->count();
        $totalPaid = Payment::where('created_at', '>=', $startDate)
            ->where('status', 'paid')->count();
        
        return $totalExpected > 0 ? round(($totalPaid / $totalExpected) * 100, 2) : 0;
    }

    private function calculateGrowthRate($startDate)
    {
        $currentPeriod = Payment::where('created_at', '>=', $startDate)
            ->where('status', 'paid')->sum('amount');
        
        $previousPeriod = Payment::where('created_at', '>=', $startDate->copy()->subDays($startDate->diffInDays(now())))
            ->where('created_at', '<', $startDate)
            ->where('status', 'paid')->sum('amount');
        
        return $previousPeriod > 0 ? round((($currentPeriod - $previousPeriod) / $previousPeriod) * 100, 2) : 0;
    }

    private function getRevenueChartData($startDate, $period)
    {
        $payments = Payment::where('created_at', '>=', $startDate)
            ->where('status', 'paid')
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $payments->pluck('date')->map(function($date) {
                return Carbon::parse($date)->format('d/m');
            }),
            'data' => $payments->pluck('total'),
        ];
    }

    private function getPaymentDistribution($startDate)
    {
        $distribution = Payment::where('created_at', '>=', $startDate)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return [
            'labels' => $distribution->pluck('status')->map(function($status) {
                return ucfirst($status);
            }),
            'data' => $distribution->pluck('count'),
        ];
    }

    private function getTontinePerformance($startDate)
    {
        $tontines = Tontine::withCount(['payments' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->with(['payments' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate)
                      ->where('status', 'paid');
            }])
            ->where('status', 'active')
            ->limit(10)
            ->get();

        return [
            'labels' => $tontines->pluck('name'),
            'data' => $tontines->map(function($tontine) {
                return $tontine->payments->sum('amount');
            }),
        ];
    }

    private function getAgentRanking($startDate)
    {
        $agents = User::role('agent')
            ->withCount(['payments' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate)
                      ->where('status', 'paid');
            }])
            ->with(['payments' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate)
                      ->where('status', 'paid');
            }])
            ->orderBy('payments_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'labels' => $agents->pluck('name'),
            'data' => $agents->map(function($agent) {
                return $agent->payments->sum('amount');
            }),
            'counts' => $agents->pluck('payments_count'),
        ];
    }

    public function exportPdf(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        
        $data = [
            'kpis' => [
                'total_revenue' => Payment::where('created_at', '>=', $startDate)
                    ->where('status', 'paid')->sum('amount'),
                'total_clients' => Client::where('created_at', '>=', $startDate)->count(),
                'active_tontines' => Tontine::where('status', 'active')->count(),
                'payment_rate' => $this->calculatePaymentRate($startDate),
            ],
            'period' => $period,
            'start_date' => $startDate->format('d/m/Y'),
            'end_date' => now()->format('d/m/Y'),
        ];

        $pdf = \Barryvdh\DomPDF\PDF::loadView('reports.advanced.pdf', $data);
        
        return $pdf->download('rapport-tontine-' . $period . '-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        
        return Excel::download(new TontineReportExport($startDate, $period), 
            'rapport-tontine-' . $period . '-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function predictiveAnalytics()
    {
        // Prédictions basées sur les tendances historiques
        $predictions = [
            'next_month_revenue' => $this->predictNextMonthRevenue(),
            'client_growth' => $this->predictClientGrowth(),
            'payment_trends' => $this->predictPaymentTrends(),
        ];

        return response()->json($predictions);
    }

    private function predictNextMonthRevenue()
    {
        // Analyse des 6 derniers mois pour prédire le prochain
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $monthlyRevenue = Payment::where('created_at', '>=', $sixMonthsAgo)
            ->where('status', 'paid')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        if ($monthlyRevenue->count() < 2) {
            return 0;
        }

        // Calcul de la tendance linéaire simple
        $values = $monthlyRevenue->pluck('total')->toArray();
        $trend = $this->calculateLinearTrend($values);
        
        return round($trend, 2);
    }

    private function calculateLinearTrend($values)
    {
        $n = count($values);
        if ($n < 2) return 0;

        $sumX = ($n - 1) * $n / 2; // Somme des indices 0, 1, 2, ..., n-1
        $sumY = array_sum($values);
        $sumXY = 0;
        $sumX2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $i * $values[$i];
            $sumX2 += $i * $i;
        }

        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Prédiction pour le prochain mois (indice n)
        return $slope * $n + $intercept;
    }

    private function predictClientGrowth()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $monthlyClients = Client::where('created_at', '>=', $sixMonthsAgo)
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        if ($monthlyClients->count() < 2) {
            return 0;
        }

        $values = $monthlyClients->pluck('total')->toArray();
        return round($this->calculateLinearTrend($values), 0);
    }

    private function predictPaymentTrends()
    {
        $threeMonthsAgo = Carbon::now()->subMonths(3);
        $dailyPayments = Payment::where('created_at', '>=', $threeMonthsAgo)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($dailyPayments->count() < 7) {
            return ['trend' => 'insufficient_data'];
        }

        $values = $dailyPayments->pluck('count')->toArray();
        $trend = $this->calculateLinearTrend($values);

        return [
            'trend' => $trend > 0 ? 'increasing' : ($trend < 0 ? 'decreasing' : 'stable'),
            'strength' => abs($trend),
            'prediction' => round($trend, 2),
        ];
    }
}
