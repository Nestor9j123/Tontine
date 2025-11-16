<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Tontine;
use App\Models\Payment;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use App\Exports\TontinesExport;
use App\Exports\PaymentsExport;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // Dashboard des rapports
    public function index()
    {
        $stats = [
            'clients' => Client::count(),
            'agents' => User::role('agent')->count(),
            'tontines_active' => Tontine::where('status', 'active')->count(),
            'tontines_completed' => Tontine::where('status', 'completed')->count(),
            'payments_sum' => Payment::validated()->sum('amount'),
            'payments_pending' => Payment::pending()->count(),
        ];

        return view('reports.index', compact('stats'));
    }

    // Listes
    public function clients()
    {
        $clients = Client::with('agent')->orderByDesc('created_at')->paginate(20);
        return view('reports.clients', compact('clients'));
    }

    public function tontines()
    {
        $tontines = Tontine::with(['client', 'product', 'agent'])->orderByDesc('created_at')->paginate(20);
        return view('reports.tontines', compact('tontines'));
    }

    public function payments()
    {
        $payments = Payment::with(['client', 'tontine', 'collector', 'validator'])->orderByDesc('payment_date')->paginate(20);
        return view('reports.payments', compact('payments'));
    }

    public function agents(Request $request)
    {
        $query = User::role("agent")
            ->withCount(["clients", "tontines", "payments"]);

        // Recherche
        if ($request->has("search") && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where("name", "like", "%{$search}%")
                  ->orWhere("email", "like", "%{$search}%")
                  ->orWhere("phone", "like", "%{$search}%");
            });
        }

        $agents = $query->get();
        return view("reports.agents", compact("agents"));
    }
    
    public function agentDetails(User $agent)
    {
        // Vérifier que c'est bien un agent
        if (!$agent->hasRole('agent')) {
            return redirect()->route('reports.agents')->with('error', 'Cet utilisateur n\'est pas un agent.');
        }
        
        // Récupérer les données de l'agent
        $agent->load(['clients', 'tontines.product', 'tontines.client', 'payments.client', 'payments.tontine']);
        
        // Statistiques de l'agent
        $stats = [
            'clients_count' => $agent->clients->count(),
            'tontines_count' => $agent->tontines->count(),
            'tontines_active' => $agent->tontines->where('status', 'active')->count(),
            'tontines_completed' => $agent->tontines->where('status', 'completed')->count(),
            'payments_count' => $agent->payments->count(),
            'payments_sum' => $agent->payments->where('status', 'validated')->sum('amount'),
            'payments_pending' => $agent->payments->where('status', 'pending')->count(),
        ];
        
        // Clients récents
        $recentClients = $agent->clients()->latest()->take(5)->get();
        
        // Tontines récentes
        $recentTontines = $agent->tontines()->with('client', 'product')->latest()->take(5)->get();
        
        // Paiements récents
        $recentPayments = $agent->payments()->with('client', 'tontine')->latest()->take(5)->get();
        
        return view('reports.agent-details', compact('agent', 'stats', 'recentClients', 'recentTontines', 'recentPayments'));
    }

    // Exports Excel
    public function exportClients()
    {
        return Excel::download(new ClientsExport, 'clients.xlsx');
    }

    public function exportTontines()
    {
        return Excel::download(new TontinesExport, 'tontines.xlsx');
    }

    public function exportPayments()
    {
        return Excel::download(new PaymentsExport, 'payments.xlsx');
    }

    // Export PDF
    public function exportPaymentPdf(Payment $payment)
    {
        $pdf = Pdf::loadView('reports.pdf.payment', [
            'payment' => $payment->load(['client', 'tontine.product', 'collector', 'validator'])
        ]);
        return $pdf->download("recu-{$payment->reference}.pdf");
    }
    
    public function agentPayments(User $agent)
    {
        // Vérifier que c'est bien un agent
        if (!$agent->hasRole('agent')) {
            return redirect()->route('reports.agents')->with('error', 'Cet utilisateur n\'est pas un agent.');
        }
        
        // Récupérer les paiements de l'agent
        $payments = Payment::where('collected_by', $agent->id)
            ->with(['client', 'tontine.product'])
            ->orderBy('payment_date', 'desc')
            ->get();
        
        // Grouper les paiements par mois
        $paymentsByMonth = $payments->groupBy(function($payment) {
            return $payment->payment_date->format('Y-m');
        });
        
        // Calculer les totaux et commissions par mois
        $monthlyStats = [];
        foreach ($paymentsByMonth as $month => $monthPayments) {
            $totalAmount = $monthPayments->where('status', 'validated')->sum('amount');
            $commission = $totalAmount * 0.1; // 10% de commission
            
            $monthlyStats[$month] = [
                'month_name' => \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                'payments_count' => $monthPayments->count(),
                'total_amount' => $totalAmount,
                'commission' => $commission,
                'payments' => $monthPayments
            ];
        }
        
        // Statistiques globales
        $totalCommission = array_sum(array_column($monthlyStats, 'commission'));
        $totalCollected = array_sum(array_column($monthlyStats, 'total_amount'));
        
        return view('reports.agent-payments', compact('agent', 'monthlyStats', 'totalCommission', 'totalCollected'));
    }
}
