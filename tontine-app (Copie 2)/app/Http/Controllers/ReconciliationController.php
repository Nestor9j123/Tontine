<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReconciliationController extends Controller
{
    /**
     * Rapport de réconciliation pour détecter les anomalies
     */
    public function index(Request $request)
    {
        $agentId = $request->get('agent_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        // Récupérer tous les agents
        $agents = User::role('agent')->get();

        // Si un agent spécifique est sélectionné
        if ($agentId) {
            $agent = User::findOrFail($agentId);
            
            // Statistiques de l'agent
            $stats = $this->getAgentStats($agentId, $startDate, $endDate);
            
            // Paiements suspects (montants arrondis, montants identiques répétés, etc.)
            $suspiciousPayments = $this->detectSuspiciousPayments($agentId, $startDate, $endDate);
            
            // Historique des modifications
            $modifications = $this->getPaymentModifications($agentId, $startDate, $endDate);
            
            return view('reconciliation.show', compact('agent', 'stats', 'suspiciousPayments', 'modifications', 'agents', 'startDate', 'endDate'));
        }

        // Vue d'ensemble de tous les agents
        $overview = $this->getOverview($startDate, $endDate);
        
        return view('reconciliation.index', compact('overview', 'agents', 'startDate', 'endDate'));
    }

    private function getAgentStats($agentId, $startDate, $endDate)
    {
        return [
            'total_payments' => Payment::byAgent($agentId)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->count(),
            
            'pending_payments' => Payment::byAgent($agentId)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->pending()
                ->count(),
            
            'validated_payments' => Payment::byAgent($agentId)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->validated()
                ->count(),
            
            'rejected_payments' => Payment::byAgent($agentId)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->where('status', 'rejected')
                ->count(),
            
            'total_amount_pending' => Payment::byAgent($agentId)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->pending()
                ->sum('amount'),
            
            'total_amount_validated' => Payment::byAgent($agentId)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->validated()
                ->sum('amount'),
            
            'average_payment' => Payment::byAgent($agentId)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->avg('amount'),
            
            'rejection_rate' => $this->calculateRejectionRate($agentId, $startDate, $endDate),
        ];
    }

    private function detectSuspiciousPayments($agentId, $startDate, $endDate)
    {
        $suspicious = [];
        
        // 1. Montants arrondis suspects (ex: 10000, 20000, 50000)
        $roundedAmounts = Payment::byAgent($agentId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->whereRaw('amount % 10000 = 0')
            ->where('amount', '>=', 10000)
            ->get();
        
        foreach ($roundedAmounts as $payment) {
            $suspicious[] = [
                'payment' => $payment,
                'reason' => 'Montant très arrondi',
                'severity' => 'medium',
            ];
        }
        
        // 2. Montants identiques répétés le même jour
        $duplicates = Payment::byAgent($agentId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->select('amount', 'payment_date', DB::raw('COUNT(*) as payment_count'))
            ->groupBy('amount', 'payment_date')
            ->havingRaw('COUNT(*) > 2')
            ->get();
        
        foreach ($duplicates as $dup) {
            $payments = Payment::byAgent($agentId)
                ->where('amount', $dup->amount)
                ->whereDate('payment_date', $dup->payment_date)
                ->get();
            
            foreach ($payments as $payment) {
                $suspicious[] = [
                    'payment' => $payment,
                    'reason' => "Montant identique répété {$dup->payment_count} fois le même jour",
                    'severity' => 'high',
                ];
            }
        }
        
        // 3. Paiements juste en dessous du seuil de validation (si existe)
        $nearThreshold = Payment::byAgent($agentId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->whereBetween('amount', [9500, 9999])
            ->get();
        
        foreach ($nearThreshold as $payment) {
            $suspicious[] = [
                'payment' => $payment,
                'reason' => 'Montant juste sous un seuil suspect',
                'severity' => 'low',
            ];
        }
        
        // 4. Paiements modifiés après création
        $modified = \App\Models\ActivityLog::where('model_type', 'Payment')
            ->where('action', 'update')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        foreach ($modified as $log) {
            // Récupérer le paiement via model_id
            $payment = Payment::find($log->model_id);
            if ($payment && $payment->collected_by == $agentId) {
                $suspicious[] = [
                    'payment' => $payment,
                    'reason' => 'Paiement modifié après création',
                    'severity' => 'high',
                    'modified_at' => $log->created_at,
                ];
            }
        }
        
        return collect($suspicious)->sortByDesc('severity');
    }

    private function getPaymentModifications($agentId, $startDate, $endDate)
    {
        $logs = \App\Models\ActivityLog::where('model_type', 'Payment')
            ->whereIn('action', ['update', 'delete'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('user')
            ->latest()
            ->get();
        
        // Filtrer pour ne garder que les paiements de cet agent
        return $logs->filter(function($log) use ($agentId) {
            $payment = Payment::find($log->model_id);
            return $payment && $payment->collected_by == $agentId;
        });
    }

    private function calculateRejectionRate($agentId, $startDate, $endDate)
    {
        $total = Payment::byAgent($agentId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->count();
        
        if ($total == 0) return 0;
        
        $rejected = Payment::byAgent($agentId)
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'rejected')
            ->count();
        
        return round(($rejected / $total) * 100, 2);
    }

    private function getOverview($startDate, $endDate)
    {
        $agents = User::role('agent')->get();
        $overview = [];
        
        foreach ($agents as $agent) {
            $stats = $this->getAgentStats($agent->id, $startDate, $endDate);
            $suspicious = $this->detectSuspiciousPayments($agent->id, $startDate, $endDate);
            
            $overview[] = [
                'agent' => $agent,
                'stats' => $stats,
                'suspicious_count' => $suspicious->count(),
                'risk_level' => $this->calculateRiskLevel($stats, $suspicious->count()),
            ];
        }
        
        return collect($overview)->sortByDesc('risk_level');
    }

    private function calculateRiskLevel($stats, $suspiciousCount)
    {
        $score = 0;
        
        // Taux de rejet élevé
        if ($stats['rejection_rate'] > 20) $score += 3;
        elseif ($stats['rejection_rate'] > 10) $score += 2;
        elseif ($stats['rejection_rate'] > 5) $score += 1;
        
        // Paiements suspects
        if ($suspiciousCount > 10) $score += 3;
        elseif ($suspiciousCount > 5) $score += 2;
        elseif ($suspiciousCount > 0) $score += 1;
        
        // Beaucoup de paiements en attente
        if ($stats['pending_payments'] > 20) $score += 2;
        elseif ($stats['pending_payments'] > 10) $score += 1;
        
        return $score;
    }
}
