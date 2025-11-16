<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Tontine;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdvancedReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:secretary|super_admin']);
    }

    /**
     * Dashboard principal des rapports
     */
    public function index()
    {
        $data = [
            'kpis' => $this->getKPIs(),
            'chartData' => $this->getChartData(),
            'topProducts' => $this->getTopProducts(),
            'recentActivity' => $this->getRecentActivity(),
        ];

        return view('reports.advanced', $data);
    }

    /**
     * KPIs principaux
     */
    private function getKPIs()
    {
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        return [
            'total_clients' => [
                'current' => Client::count(),
                'previous' => Client::where('created_at', '<', $currentMonth)->count(),
            ],
            'active_tontines' => [
                'current' => Tontine::where('status', 'active')->count(),
                'previous' => Tontine::where('status', 'active')->where('created_at', '<', $currentMonth)->count(),
            ],
            'monthly_revenue' => [
                'current' => Payment::whereMonth('created_at', now()->month)->sum('amount'),
                'previous' => Payment::whereMonth('created_at', now()->subMonth()->month)->sum('amount'),
            ],
            'completion_rate' => [
                'current' => $this->getCompletionRate(),
                'previous' => $this->getCompletionRate($lastMonth),
            ],
        ];
    }

    /**
     * Données pour les graphiques
     */
    private function getChartData()
    {
        return [
            'monthly_payments' => $this->getMonthlyPayments(),
            'tontines_by_status' => $this->getTontinesByStatus(),
            'clients_growth' => $this->getClientsGrowth(),
            'products_performance' => $this->getProductsPerformance(),
        ];
    }

    /**
     * Paiements mensuels (12 derniers mois)
     */
    private function getMonthlyPayments()
    {
        $payments = Payment::select(
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('EXTRACT(MONTH FROM created_at) as month'),
            DB::raw('SUM(amount) as total'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        $labels = [];
        $amounts = [];
        $counts = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $payment = $payments->where('year', $date->year)->where('month', $date->month)->first();
            $amounts[] = $payment ? $payment->total : 0;
            $counts[] = $payment ? $payment->count : 0;
        }

        return [
            'labels' => $labels,
            'amounts' => $amounts,
            'counts' => $counts,
        ];
    }

    /**
     * Répartition des tontines par statut
     */
    private function getTontinesByStatus()
    {
        $statuses = Tontine::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return [
            'labels' => $statuses->pluck('status')->toArray(),
            'data' => $statuses->pluck('count')->toArray(),
        ];
    }

    /**
     * Croissance des clients (6 derniers mois)
     */
    private function getClientsGrowth()
    {
        $growth = Client::select(
            DB::raw('EXTRACT(YEAR FROM created_at) as year'),
            DB::raw('EXTRACT(MONTH FROM created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get();

        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');
            
            $client = $growth->where('year', $date->year)->where('month', $date->month)->first();
            $data[] = $client ? $client->count : 0;
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    /**
     * Performance des produits
     */
    private function getProductsPerformance()
    {
        $products = Product::select('products.name')
            ->join('tontines', 'products.id', '=', 'tontines.product_id')
            ->select('products.name', DB::raw('COUNT(tontines.id) as tontines_count'), DB::raw('SUM(tontines.total_amount) as total_amount'))
            ->groupBy('products.id', 'products.name')
            ->orderBy('tontines_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'labels' => $products->pluck('name')->toArray(),
            'tontines' => $products->pluck('tontines_count')->toArray(),
            'amounts' => $products->pluck('total_amount')->toArray(),
        ];
    }

    /**
     * Top produits
     */
    private function getTopProducts()
    {
        return Product::select('products.*')
            ->join('tontines', 'products.id', '=', 'tontines.product_id')
            ->select('products.*', DB::raw('COUNT(tontines.id) as tontines_count'), DB::raw('SUM(tontines.total_amount) as total_revenue'))
            ->groupBy('products.id')
            ->orderBy('tontines_count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Activité récente
     */
    private function getRecentActivity()
    {
        $recentPayments = Payment::with(['client', 'tontine'])
            ->latest()
            ->limit(10)
            ->get();

        $recentTontines = Tontine::with(['client', 'product'])
            ->latest()
            ->limit(5)
            ->get();

        return [
            'payments' => $recentPayments,
            'tontines' => $recentTontines,
        ];
    }

    /**
     * Taux de completion
     */
    private function getCompletionRate($date = null)
    {
        $query = Tontine::query();
        
        if ($date) {
            $query->where('created_at', '>=', $date);
        }

        $total = $query->count();
        $completed = $query->where('status', 'completed')->count();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        $data = [
            'kpis' => $this->getKPIs(),
            'topProducts' => $this->getTopProducts(),
            'date' => now()->format('d/m/Y'),
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.pdf', $data);
        
        return $pdf->download('rapport-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        // TODO: Implémenter l'export Excel avec Maatwebsite\Excel
        return response()->json(['message' => 'Export Excel en cours de développement']);
    }
}
