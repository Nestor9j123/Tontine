<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Client;
use App\Models\User;
use App\Services\PartialPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartialPaymentController extends Controller
{
    protected $partialPaymentService;

    public function __construct(PartialPaymentService $partialPaymentService)
    {
        $this->middleware('auth');
        $this->middleware('role:secretary|super_admin');
        
        $this->partialPaymentService = $partialPaymentService;
    }

    /**
     * Liste des paiements partiels et manquants
     */
    public function index(Request $request)
    {
        $filters = $request->only(['payment_type', 'agent_id', 'client_id', 'date_from', 'date_to']);
        
        $payments = $this->partialPaymentService->getPartialPaymentsWithFilters($filters);
        $stats = $this->partialPaymentService->getPartialPaymentStats();
        
        // Pour les filtres
        $agents = User::role('agent')->orderBy('name')->get(['id', 'name']);
        $clients = Client::orderBy('first_name')->get(['id', 'first_name', 'last_name']);

        return view('partial-payments.index', compact('payments', 'stats', 'agents', 'clients', 'filters'));
    }

    /**
     * Afficher les détails d'un paiement partiel avec historique
     */
    public function show(Payment $payment)
    {
        $payment->load(['client', 'collector', 'tontine', 'completedBy']);
        $history = $this->partialPaymentService->getPaymentHistory($payment);

        return view('partial-payments.show', compact('payment', 'history'));
    }

    /**
     * Formulaire pour ajouter un paiement manquant
     */
    public function addMissingForm(Payment $payment)
    {
        if (!$payment->has_missing_payment || $payment->remaining_missing_amount <= 0) {
            return redirect()->route('partial-payments.show', $payment)
                ->with('error', 'Ce paiement n\'a pas de montant manquant à compléter.');
        }

        return view('partial-payments.add-missing', compact('payment'));
    }

    /**
     * Traiter l'ajout d'un paiement manquant
     */
    public function storeMissingPayment(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $payment->remaining_missing_amount,
            'notes' => 'nullable|string|max:1000',
        ], [
            'amount.max' => 'Le montant ne peut pas dépasser ' . $payment->formatted_remaining_missing_amount,
        ]);

        try {
            $this->partialPaymentService->addMissingPayment(
                $payment,
                $request->amount,
                auth()->id(),
                $request->notes
            );

            $message = $payment->fresh()->remaining_missing_amount <= 0 
                ? 'Paiement complété avec succès ! Le montant manquant a été entièrement payé.'
                : 'Paiement partiel ajouté avec succès. Reste ' . $payment->fresh()->formatted_remaining_missing_amount . ' à payer.';

            return redirect()->route('partial-payments.show', $payment)
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajout du paiement : ' . $e->getMessage());
        }
    }

    /**
     * Historique complet d'un paiement (AJAX)
     */
    public function history(Payment $payment)
    {
        $history = $this->partialPaymentService->getPaymentHistory($payment);
        
        return response()->json([
            'payment' => [
                'reference' => $payment->reference,
                'client_name' => $payment->client->name,
                'agent_name' => $payment->collector->name,
                'tontine_name' => $payment->tontine->name,
                'payment_status' => $payment->payment_status,
            ],
            'history' => $history->map(function ($item) {
                return [
                    'action_type' => $item->action_type,
                    'action_type_label' => $item->action_type_label,
                    'amount' => $item->formatted_amount,
                    'remaining_amount' => $item->formatted_remaining_amount,
                    'notes' => $item->notes,
                    'recorded_by' => $item->recordedBy->name,
                    'action_date' => $item->action_date->format('d/m/Y H:i'),
                ];
            })
        ]);
    }

    /**
     * Statistiques des paiements partiels par agent
     */
    public function agentStats($agentId)
    {
        $agent = User::findOrFail($agentId);
        $pendingPayments = $this->partialPaymentService->getAgentPendingPayments($agentId);
        
        $stats = [
            'total_pending' => $pendingPayments->count(),
            'total_missing_amount' => $pendingPayments->sum('remaining_missing_amount'),
            'oldest_pending' => $pendingPayments->min('payment_date'),
        ];

        return view('partial-payments.agent-stats', compact('agent', 'pendingPayments', 'stats'));
    }

    /**
     * Statistiques des paiements partiels par client
     */
    public function clientStats($clientId)
    {
        $client = Client::findOrFail($clientId);
        $pendingPayments = $this->partialPaymentService->getClientPendingPayments($clientId);
        
        $stats = [
            'total_pending' => $pendingPayments->count(),
            'total_missing_amount' => $pendingPayments->sum('remaining_missing_amount'),
            'oldest_pending' => $pendingPayments->min('payment_date'),
        ];

        return view('partial-payments.client-stats', compact('client', 'pendingPayments', 'stats'));
    }
}
