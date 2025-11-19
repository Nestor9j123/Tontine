<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentHistory;
use Illuminate\Support\Facades\DB;
use Exception;

class PartialPaymentService
{
    /**
     * Créer un paiement avec possibilité de montant partiel
     */
    public function createPayment(array $data, $recordedBy)
    {
        return DB::transaction(function () use ($data, $recordedBy) {
            $expectedAmount = $data['expected_amount'] ?? null;
            $actualAmount = $data['amount'];
            $missingAmount = 0;
            $isPartial = false;
            $hasMissing = false;
            $paymentStatus = 'complete';

            // Calculer s'il y a un montant manquant
            if ($expectedAmount && $actualAmount < $expectedAmount) {
                $missingAmount = $expectedAmount - $actualAmount;
                $isPartial = true;
                $hasMissing = true;
                $paymentStatus = 'partial';
            }

            // Créer le paiement avec les nouveaux champs
            $payment = Payment::create([
                'reference' => $data['reference'] ?? null,
                'tontine_id' => $data['tontine_id'],
                'client_id' => $data['client_id'],
                'collected_by' => $data['collected_by'],
                'amount' => $actualAmount,
                'expected_amount' => $expectedAmount,
                'missing_amount' => $missingAmount,
                'is_partial_payment' => $isPartial,
                'has_missing_payment' => $hasMissing,
                'missing_paid_amount' => 0,
                'payment_status' => $paymentStatus,
                'daily_amount' => $data['daily_amount'] ?? null,
                'days_count' => $data['days_count'] ?? null,
                'is_multiple_payment' => $data['is_multiple_payment'] ?? false,
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'] ?? 'cash',
                'transaction_id' => $data['transaction_id'] ?? null,
                'notes' => $data['notes'] ?? null,
                'missing_notes' => $data['missing_notes'] ?? null,
                'status' => $data['status'] ?? 'validated', // Validé directement par le secrétaire
                'validated_by' => $recordedBy,
                'validated_at' => now(),
            ]);

            // Créer l'historique initial
            $payment->createInitialPaymentHistory($recordedBy);

            return $payment;
        });
    }

    /**
     * Ajouter un paiement pour compléter le montant manquant
     */
    public function addMissingPayment(Payment $payment, $amount, $recordedBy, $notes = null)
    {
        if (!$payment->has_missing_payment) {
            throw new Exception('Ce paiement n\'a pas de montant manquant');
        }

        $remainingAmount = $payment->remaining_missing_amount;
        
        if ($amount > $remainingAmount) {
            throw new Exception("Le montant ({$amount} FCFA) dépasse le montant manquant restant ({$remainingAmount} FCFA)");
        }

        return DB::transaction(function () use ($payment, $amount, $recordedBy, $notes) {
            return $payment->addMissingPayment($amount, $recordedBy, $notes);
        });
    }

    /**
     * Obtenir l'historique complet d'un paiement
     */
    public function getPaymentHistory(Payment $payment)
    {
        return $payment->paymentHistory()
            ->with(['recordedBy:id,name', 'agent:id,name'])
            ->orderBy('action_date')
            ->get();
    }

    /**
     * Obtenir tous les paiements avec montant manquant pour un agent
     */
    public function getAgentPendingPayments($agentId)
    {
        return Payment::where('collected_by', $agentId)
            ->withMissingAmount()
            ->where('missing_amount', '>', DB::raw('missing_paid_amount'))
            ->with(['client:id,first_name,last_name,phone', 'tontine:id,code,product_id', 'tontine.product:id,name'])
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    /**
     * Obtenir tous les paiements avec montant manquant pour un client
     */
    public function getClientPendingPayments($clientId)
    {
        return Payment::where('client_id', $clientId)
            ->withMissingAmount()
            ->where('missing_amount', '>', DB::raw('missing_paid_amount'))
            ->with(['collector:id,name', 'tontine:id,name'])
            ->orderBy('payment_date', 'desc')
            ->get();
    }

    /**
     * Statistiques des paiements partiels
     */
    public function getPartialPaymentStats()
    {
        return [
            'total_partial_payments' => Payment::partialPayments()->count(),
            'total_missing_amount' => Payment::withMissingAmount()
                ->selectRaw('SUM(missing_amount - missing_paid_amount) as total')
                ->value('total') ?? 0,
            'completed_missing_payments' => Payment::missingPaid()->count(),
            'pending_missing_payments' => Payment::withMissingAmount()
                ->whereRaw('missing_amount > missing_paid_amount')
                ->count(),
        ];
    }

    /**
     * Obtenir les paiements partiels avec filtres
     */
    public function getPartialPaymentsWithFilters(array $filters = [])
    {
        $query = Payment::query()
            ->with(['client:id,first_name,last_name,phone', 'collector:id,uuid,name', 'tontine:id,code,product_id', 'tontine.product:id,name']);

        // Filtrer seulement les paiements avec montant manquant ou partiels
        if (isset($filters['payment_type'])) {
            switch ($filters['payment_type']) {
                case 'partial':
                    $query->partialPayments();
                    break;
                case 'missing':
                    $query->withMissingAmount()->whereRaw('missing_amount > missing_paid_amount');
                    break;
                case 'completed':
                    $query->missingPaid();
                    break;
                default:
                    $query->where(function ($q) {
                        $q->partialPayments()->orWhere('has_missing_payment', true);
                    });
            }
        } else {
            $query->where(function ($q) {
                $q->partialPayments()->orWhere('has_missing_payment', true);
            });
        }

        // Filtres additionnels
        if (isset($filters['agent_id'])) {
            $query->byAgent($filters['agent_id']);
        }

        if (isset($filters['client_id'])) {
            $query->byClient($filters['client_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('payment_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('payment_date', '<=', $filters['date_to']);
        }

        return $query->orderBy('payment_date', 'desc')->paginate(15);
    }
}
