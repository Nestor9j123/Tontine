<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Tontine;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientDeletionService
{
    /**
     * Supprimer un client avec toutes ses données liées
     */
    public function deleteClientWithRelatedData(Client $client, bool $forceDelete = false)
    {
        return DB::transaction(function () use ($client, $forceDelete) {
            $deletionSummary = [
                'client_id' => $client->id,
                'client_name' => $client->full_name,
                'tontines_count' => 0,
                'payments_count' => 0,
                'notebook_payments_count' => 0,
                'total_amount_affected' => 0,
            ];

            // 1. Compter et supprimer les tontines
            $tontines = $client->tontines()->withTrashed()->get();
            $deletionSummary['tontines_count'] = $tontines->count();
            
            foreach ($tontines as $tontine) {
                $deletionSummary['total_amount_affected'] += $tontine->paid_amount;
                
                if ($forceDelete) {
                    $tontine->forceDelete();
                } else {
                    $tontine->delete();
                }
            }

            // 2. Compter et supprimer les paiements
            $payments = $client->payments()->withTrashed()->get();
            $deletionSummary['payments_count'] = $payments->count();
            
            foreach ($payments as $payment) {
                if ($forceDelete) {
                    $payment->forceDelete();
                } else {
                    $payment->delete();
                }
            }

            // 3. Compter et supprimer les paiements carnet
            $notebookPayments = $client->notebookPayments()->withTrashed()->get();
            $deletionSummary['notebook_payments_count'] = $notebookPayments->count();
            
            foreach ($notebookPayments as $notebookPayment) {
                if ($forceDelete) {
                    $notebookPayment->forceDelete();
                } else {
                    $notebookPayment->delete();
                }
            }

            // 4. Supprimer le client
            if ($forceDelete) {
                $client->forceDelete();
            } else {
                $client->delete();
            }

            // 5. Logger l'activité
            Log::info($forceDelete ? 'Client définitivement supprimé avec toutes ses données' : 'Client supprimé (soft delete) avec toutes ses données', array_merge($deletionSummary, [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'System',
                'timestamp' => now()
            ]));

            Log::info('Client deletion completed', $deletionSummary);

            return $deletionSummary;
        });
    }

    /**
     * Restaurer un client avec toutes ses données liées
     */
    public function restoreClientWithRelatedData(Client $client)
    {
        return DB::transaction(function () use ($client) {
            $restorationSummary = [
                'client_id' => $client->id,
                'client_name' => $client->full_name,
                'tontines_restored' => 0,
                'payments_restored' => 0,
                'notebook_payments_restored' => 0,
            ];

            // 1. Restaurer le client
            $client->restore();

            // 2. Restaurer les tontines
            $tontines = $client->tontines()->onlyTrashed()->get();
            foreach ($tontines as $tontine) {
                $tontine->restore();
                $restorationSummary['tontines_restored']++;
            }

            // 3. Restaurer les paiements
            $payments = $client->payments()->onlyTrashed()->get();
            foreach ($payments as $payment) {
                $payment->restore();
                $restorationSummary['payments_restored']++;
            }

            // 4. Restaurer les paiements carnet
            $notebookPayments = $client->notebookPayments()->onlyTrashed()->get();
            foreach ($notebookPayments as $notebookPayment) {
                $notebookPayment->restore();
                $restorationSummary['notebook_payments_restored']++;
            }

            // 5. Logger l'activité
            Log::info('Client restauré avec toutes ses données liées', array_merge($restorationSummary, [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name ?? 'System',
                'timestamp' => now()
            ]));

            return $restorationSummary;
        });
    }

    /**
     * Obtenir un résumé des données qui seraient supprimées
     */
    public function getDeletionPreview(Client $client)
    {
        return [
            'client' => [
                'id' => $client->id,
                'name' => $client->full_name,
                'phone' => $client->phone,
                'created_at' => $client->created_at,
            ],
            'tontines' => [
                'count' => $client->tontines()->count(),
                'total_amount' => $client->tontines()->sum('total_amount'),
                'paid_amount' => $client->tontines()->sum('paid_amount'),
            ],
            'payments' => [
                'count' => $client->payments()->count(),
                'total_amount' => $client->payments()->sum('amount'),
            ],
            'notebook_payments' => [
                'count' => $client->notebookPayments()->count(),
                'total_amount' => $client->notebookPayments()->sum('amount'),
            ],
        ];
    }
}
