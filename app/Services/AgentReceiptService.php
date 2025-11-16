<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Models\MonthlyExpense;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AgentReceiptService
{
    /**
     * Générer le reçu mensuel d'un agent
     */
    public function generateMonthlyReceipt(User $agent, $month, $year)
    {
        if (!$agent->hasRole('agent')) {
            throw new \InvalidArgumentException('L\'utilisateur doit être un agent.');
        }

        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

        // Collecter les données de performance de l'agent
        $data = [
            'agent' => $agent,
            'period' => [
                'month' => $month,
                'year' => $year,
                'start_date' => $startOfMonth,
                'end_date' => $endOfMonth,
                'period_name' => $startOfMonth->locale('fr')->isoFormat('MMMM Y')
            ],
            'payments' => $this->getAgentPayments($agent, $startOfMonth, $endOfMonth),
            'expenses' => $this->getAgentExpenses($agent, $month, $year),
            'summary' => $this->calculateAgentSummary($agent, $startOfMonth, $endOfMonth, $month, $year),
            'clients' => $this->getAgentClients($agent, $startOfMonth, $endOfMonth),
        ];

        return $data;
    }

    /**
     * Obtenir les paiements collectés par l'agent
     */
    private function getAgentPayments(User $agent, Carbon $startDate, Carbon $endDate)
    {
        return Payment::with(['client', 'tontine.product'])
                     ->where('collected_by', $agent->id)
                     ->whereBetween('payment_date', [$startDate, $endDate])
                     ->orderBy('payment_date')
                     ->get()
                     ->groupBy(function($payment) {
                         return $payment->payment_date->format('Y-m-d');
                     });
    }

    /**
     * Obtenir les dépenses de l'agent
     */
    private function getAgentExpenses(User $agent, $month, $year)
    {
        return MonthlyExpense::where('user_id', $agent->id)
                            ->forMonth($month, $year)
                            ->orderBy('expense_date')
                            ->get();
    }

    /**
     * Calculer le résumé de performance de l'agent
     */
    private function calculateAgentSummary(User $agent, Carbon $startDate, Carbon $endDate, $month, $year)
    {
        $paymentsQuery = Payment::where('collected_by', $agent->id)
                               ->whereBetween('payment_date', [$startDate, $endDate]);

        $expenses = MonthlyExpense::where('user_id', $agent->id)
                                 ->forMonth($month, $year)
                                 ->sum('amount');

        // Calculer les commissions/bénéfices
        $validatedAmount = $paymentsQuery->where('status', 'validated')->sum('amount');
        $commissionRate = 0.10; // 10% de commission par défaut
        $grossCommission = $validatedAmount * $commissionRate;
        $netCommission = $grossCommission - $expenses;

        return [
            'total_payments_collected' => $paymentsQuery->count(),
            'total_amount_collected' => $validatedAmount,
            'pending_payments' => $paymentsQuery->where('status', 'pending')->count(),
            'pending_amount' => $paymentsQuery->where('status', 'pending')->sum('amount'),
            'validated_payments' => $paymentsQuery->where('status', 'validated')->count(),
            'rejected_payments' => $paymentsQuery->where('status', 'rejected')->count(),
            'total_expenses' => $expenses,
            'commission_rate' => $commissionRate,
            'gross_commission' => $grossCommission,
            'net_commission' => $netCommission,
            'new_clients' => $this->getNewClientsCount($agent, $startDate, $endDate),
            'active_tontines' => $this->getActiveTontinesCount($agent),
            'completed_tontines' => $this->getCompletedTontinesCount($agent, $startDate, $endDate),
        ];
    }

    /**
     * Obtenir les clients de l'agent pour la période
     */
    private function getAgentClients(User $agent, Carbon $startDate, Carbon $endDate)
    {
        return $agent->clients()
                    ->with(['tontines' => function($query) use ($startDate, $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate])
                              ->orWhereBetween('validated_at', [$startDate, $endDate]);
                    }])
                    ->get();
    }

    /**
     * Compter les nouveaux clients dans la période
     */
    private function getNewClientsCount(User $agent, Carbon $startDate, Carbon $endDate)
    {
        return $agent->clients()
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();
    }

    /**
     * Compter les tontines actives de l'agent
     */
    private function getActiveTontinesCount(User $agent)
    {
        return $agent->tontines()
                    ->where('status', 'active')
                    ->count();
    }

    /**
     * Compter les tontines complétées dans la période
     */
    private function getCompletedTontinesCount(User $agent, Carbon $startDate, Carbon $endDate)
    {
        return $agent->tontines()
                    ->where('status', 'completed')
                    ->whereBetween('validated_at', [$startDate, $endDate])
                    ->count();
    }

    /**
     * Générer le PDF du reçu
     */
    public function generateReceiptPdf(User $agent, $month, $year)
    {
        $data = $this->generateMonthlyReceipt($agent, $month, $year);
        
        $pdf = Pdf::loadView('agent-receipts.monthly-receipt', $data);
        
        $filename = sprintf('recu-agent-%s-%02d-%d.pdf', 
            str_slug($agent->name), $month, $year);
        
        return $pdf->download($filename);
    }

    /**
     * Obtenir les statistiques de tous les agents pour un mois
     */
    public function getAllAgentsStats($month, $year)
    {
        $agents = User::role('agent')->with(['clients', 'tontines'])->get();
        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth()->endOfDay();

        $stats = [];
        
        foreach ($agents as $agent) {
            $stats[] = [
                'agent' => $agent,
                'summary' => $this->calculateAgentSummary($agent, $startOfMonth, $endOfMonth, $month, $year),
            ];
        }

        // Trier par montant collecté décroissant
        usort($stats, function($a, $b) {
            return $b['summary']['total_amount_collected'] <=> $a['summary']['total_amount_collected'];
        });

        return $stats;
    }
}