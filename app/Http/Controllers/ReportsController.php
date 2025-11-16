<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Tontine;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        // Récupérer les vraies données avec des requêtes simples
        $totalRevenue = Payment::where('status', 'validated')->sum('amount');
        $totalClients = Client::count();
        $activeTontines = Tontine::where('status', 'active')->count();
        $totalPayments = Payment::count();
        $paidPayments = Payment::where('status', 'validated')->count();
        $paymentRate = $totalPayments > 0 ? round(($paidPayments / $totalPayments) * 100, 1) : 0;

        // Données mensuelles simplifiées
        $monthlyData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $monthlyLabels = ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aout', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        // Essayer d'obtenir les données mensuelles sans GROUP BY complexe
        try {
            $payments = Payment::where('status', 'validated')
                ->whereYear('created_at', date('Y'))
                ->get();
                
            foreach ($payments as $payment) {
                $month = (int)$payment->created_at->format('n') - 1; // 0-indexed
                if ($month >= 0 && $month < 12) {
                    $monthlyData[$month] += $payment->amount;
                }
            }
        } catch (\Exception $e) {
            // En cas d'erreur, garder les données à zéro
        }

        // Répartition des tontines par statut
        $tontineCounts = [];
        $tontineLabels = [];
        
        try {
            $tontines = Tontine::all();
            $statusCounts = [];
            
            foreach ($tontines as $tontine) {
                $status = $tontine->status;
                if (!isset($statusCounts[$status])) {
                    $statusCounts[$status] = 0;
                }
                $statusCounts[$status]++;
            }
            
            $tontineLabels = array_keys($statusCounts);
            $tontineCounts = array_values($statusCounts);
        } catch (\Exception $e) {
            // En cas d'erreur, utiliser des données par défaut
            $tontineLabels = ['Actives', 'Inactives'];
            $tontineCounts = [0, 0];
        }

        // Transactions récentes
        $transactions = collect();
        try {
            $transactions = Payment::with(['client', 'tontine'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($payment) {
                    return [
                        'date' => $payment->created_at->format('Y-m-d'),
                        'client_name' => $payment->client ? $payment->client->name : 'Client supprimé',
                        'tontine_name' => $payment->tontine ? $payment->tontine->code : 'Tontine supprimée',
                        'amount' => $payment->amount,
                        'status' => $payment->status
                    ];
                });
        } catch (\Exception $e) {
            // En cas d'erreur, garder une collection vide
        }

        return view('reports.advanced', compact(
            'totalRevenue',
            'totalClients', 
            'activeTontines',
            'paymentRate',
            'monthlyLabels',
            'monthlyData',
            'tontineLabels',
            'tontineCounts',
            'transactions'
        ));
    }
    
    public function getData(Request $request)
    {
        try {
            $payments = Payment::with(['client', 'tontine'])->get();
            
            return response()->json([
                'kpis' => [
                    'total_revenue' => $payments->where('status', 'validated')->sum('amount'),
                    'total_clients' => Client::count(),
                    'active_tontines' => Tontine::where('status', 'active')->count(),
                    'payment_rate' => $payments->count() > 0 ? round(($payments->where('status', 'validated')->count() / $payments->count()) * 100, 1) : 0
                ],
                'charts' => [
                    'payments' => [
                        'labels' => ['Jan', 'Fev', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aout', 'Sep', 'Oct', 'Nov', 'Dec'],
                        'data' => [10000, 15000, 12000, 18000, 20000, 22000, 19000, 25000, 21000, 23000, 24000, 26000]
                    ],
                    'tontines' => [
                        'labels' => ['Actives', 'Inactives', 'En attente'],
                        'data' => [25, 10, 5]
                    ]
                ],
                'table' => $payments->take(20)->map(function ($payment) {
                    return [
                        'date' => $payment->created_at->format('Y-m-d'),
                        'client_name' => $payment->client ? $payment->client->name : 'Client supprimé',
                        'tontine_name' => $payment->tontine ? $payment->tontine->code : 'Tontine supprimée',
                        'amount' => $payment->amount,
                        'status' => $payment->status
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erreur lors de la récupération des données',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function export($type)
    {
        // Export simplifié
        if ($type === 'pdf') {
            return response()->json(['message' => 'Export PDF en cours de développement']);
        } elseif ($type === 'excel') {
            return response()->json(['message' => 'Export Excel en cours de développement']);
        }
        
        return back()->with('error', 'Type d\'export non supporté');
    }
}
