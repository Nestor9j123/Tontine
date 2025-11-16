<?php

namespace App\Http\Controllers;

use App\Models\MonthlyReport;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Tontine;
use App\Models\MonthlyExpense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MonthlyReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:secretary|super_admin');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reports = MonthlyReport::with('generator')
                              ->orderByDesc('report_year')
                              ->orderByDesc('report_month')
                              ->paginate(12);

        return view('monthly-reports.index', compact('reports'));
    }

    /**
     * Generate a new monthly report
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
        ]);

        $month = $validated['month'];
        $year = $validated['year'];

        // Vérifier si le rapport existe déjà
        $existingReport = MonthlyReport::forMonth($month, $year)->first();
        if ($existingReport) {
            return redirect()->back()->with('error', 'Un rapport pour cette période existe déjà.');
        }

        try {
            DB::beginTransaction();

            $report = $this->generateMonthlyReport($month, $year);

            DB::commit();

            return redirect()->route('monthly-reports.show', $report)
                           ->with('success', 'Rapport mensuel généré avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur lors de la génération du rapport : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MonthlyReport $monthlyReport)
    {
        $monthlyReport->load('generator');
        return view('monthly-reports.show', compact('monthlyReport'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MonthlyReport $monthlyReport)
    {
        return view('monthly-reports.edit', compact('monthlyReport'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MonthlyReport $monthlyReport)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $monthlyReport->update($validated);

        return redirect()->route('monthly-reports.show', $monthlyReport)
                        ->with('success', 'Rapport mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     * Seuls les super admins peuvent supprimer les rapports
     */
    public function destroy(MonthlyReport $monthlyReport)
    {
        // Vérification stricte des permissions
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Seuls les Super Administrateurs peuvent supprimer les rapports mensuels.');
        }

        // Log de sécurité pour traçabilité
        \Log::warning('Suppression de rapport mensuel', [
            'rapport_id' => $monthlyReport->id,
            'periode' => $monthlyReport->report_month . '/' . $monthlyReport->report_year,
            'supprime_par' => auth()->user()->name,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $monthlyReport->forceDelete(); // Suppression définitive pour éviter les conflits de contrainte unique

        return redirect()->route('monthly-reports.index')
                        ->with('success', 'Rapport supprimé avec succès. Cette action a été enregistrée dans les logs de sécurité.');
    }

    /**
     * Générer le rapport mensuel automatiquement
     */
    private function generateMonthlyReport($month, $year)
    {
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();
        $startOfPrevMonth = $startOfMonth->copy()->subMonth()->startOfMonth();
        $endOfPrevMonth = $startOfMonth->copy()->subSecond();

        // Stock initial (fin du mois précédent)
        $initialStock = [];
        $products = Product::all();
        foreach ($products as $product) {
            $initialStock[$product->id] = [
                'name' => $product->name,
                'quantity' => $this->getStockAtDate($product->id, $startOfMonth->copy()->subSecond()),
            ];
        }

        // Stock final (fin du mois actuel)
        $finalStock = [];
        foreach ($products as $product) {
            $finalStock[$product->id] = [
                'name' => $product->name,
                'quantity' => $this->getStockAtDate($product->id, $endOfMonth),
            ];
        }

        // Produits vendus (tontines complétées dans le mois)
        $completedTontines = Tontine::whereBetween('validated_at', [$startOfMonth, $endOfMonth])
                                   ->where('status', 'completed')
                                   ->with('product')
                                   ->get();

        $productsSold = [];
        foreach ($completedTontines as $tontine) {
            $productId = $tontine->product_id;
            if (!isset($productsSold[$productId])) {
                $productsSold[$productId] = [
                    'name' => $tontine->product->name,
                    'quantity' => 0,
                    'revenue' => 0,
                ];
            }
            $productsSold[$productId]['quantity']++;
            $productsSold[$productId]['revenue'] += $tontine->total_amount;
        }

        // Chiffre d'affaires total
        $totalRevenue = Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                              ->where('status', 'validated')
                              ->sum('amount');

        // Total des charges
        $totalExpenses = MonthlyExpense::forMonth($month, $year)->sum('amount');

        // Statistiques des paiements
        $paymentStats = [
            'total_payments' => Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])->count(),
            'validated_payments' => Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                                          ->where('status', 'validated')->count(),
            'pending_payments' => Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                                        ->where('status', 'pending')->count(),
            'rejected_payments' => Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                                         ->where('status', 'rejected')->count(),
        ];

        // Performance des agents
        $agents = User::role('agent')->get();
        $agentPerformance = [];
        foreach ($agents as $agent) {
            $agentPayments = Payment::whereBetween('payment_date', [$startOfMonth, $endOfMonth])
                                  ->where('collected_by', $agent->id)
                                  ->where('status', 'validated');
            
            $agentExpenses = MonthlyExpense::forMonth($month, $year)
                                         ->where('user_id', $agent->id)
                                         ->sum('amount');

            $agentPerformance[$agent->id] = [
                'name' => $agent->name,
                'payments_count' => $agentPayments->count(),
                'payments_amount' => $agentPayments->sum('amount'),
                'expenses' => $agentExpenses,
                'clients_count' => Tontine::where('agent_id', $agent->id)
                                         ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                                         ->distinct('client_id')
                                         ->count('client_id'),
            ];
        }

        // Créer le rapport
        $report = MonthlyReport::create([
            'report_month' => $month,
            'report_year' => $year,
            'initial_stock' => $initialStock,
            'final_stock' => $finalStock,
            'products_sold' => $productsSold,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_result' => $totalRevenue - $totalExpenses,
            'payment_stats' => $paymentStats,
            'agent_performance' => $agentPerformance,
            'generated_by' => auth()->id(),
            'generated_at' => now(),
        ]);

        return $report;
    }

    /**
     * Obtenir le stock d'un produit à une date donnée
     */
    private function getStockAtDate($productId, $date)
    {
        // Cette méthode devrait calculer le stock en tenant compte des mouvements de stock
        // Pour l'instant, on retourne le stock actuel du produit
        $product = Product::find($productId);
        return $product ? $product->stock_quantity : 0;
    }

    /**
     * Vérifier si un rapport existe pour une période donnée (API)
     */
    public function checkExists(Request $request)
    {
        $month = $request->get('month');
        $year = $request->get('year');
        
        if (!$month || !$year) {
            return response()->json(['exists' => false]);
        }
        
        $exists = MonthlyReport::forMonth($month, $year)->exists();
        
        return response()->json(['exists' => $exists]);
    }

    /**
     * Exporter le rapport en PDF
     */
    public function exportPdf(MonthlyReport $monthlyReport)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('monthly-reports.pdf', compact('monthlyReport'));
        
        $filename = sprintf('rapport-mensuel-%02d-%d.pdf', $monthlyReport->report_month, $monthlyReport->report_year);
        
        return $pdf->download($filename);
    }
}
