<?php

namespace App\Services;

use App\Models\Tontine;
use App\Models\Product;
use App\Models\TontineNotification;
use App\Models\Payment;

class NotificationService
{
    /**
     * Vérifier si une tontine est terminée et créer une notification
     */
    public function checkTontineCompletion(Tontine $tontine)
    {
        // Vérifier si tous les paiements sont effectués
        if ($tontine->remaining_amount <= 0 && $tontine->status !== 'completed') {
            // Marquer la tontine comme terminée
            $tontine->update(['status' => 'completed']);
            
            // Créer une notification
            TontineNotification::createPaymentCompletedNotification($tontine);
            
            return true;
        }
        
        return false;
    }

    /**
     * Vérifier le stock faible et créer des notifications
     */
    public function checkLowStock()
    {
        $lowStockProducts = Product::where('is_active', true)
            ->whereRaw('stock_quantity <= min_stock_alert')
            ->where('stock_quantity', '>', 0)
            ->get();

        foreach ($lowStockProducts as $product) {
            // Vérifier si une notification récente existe déjà
            $existingNotification = TontineNotification::where('type', 'low_stock')
                ->where('message', 'like', "%{$product->name}%")
                ->where('created_at', '>=', now()->subDays(1))
                ->first();

            if (!$existingNotification) {
                TontineNotification::createLowStockNotification($product);
            }
        }
    }

    /**
     * Créer une notification de rappel de livraison
     */
    public function createDeliveryReminder(Tontine $tontine)
    {
        if ($tontine->status === 'completed' && $tontine->delivery_status === 'pending') {
            return TontineNotification::create([
                'tontine_id' => $tontine->id,
                'client_id' => $tontine->client_id,
                'agent_id' => $tontine->agent_id,
                'type' => 'delivery_reminder',
                'title' => 'Livraison en attente',
                'message' => "Le produit {$tontine->product->name} pour {$tontine->client->full_name} est prêt à être livré.",
            ]);
        }
        
        return null;
    }

    /**
     * Obtenir les notifications non lues pour un agent
     */
    public function getUnreadNotificationsForAgent($agentId)
    {
        return TontineNotification::unread()
            ->forAgent($agentId)
            ->with(['tontine.product', 'client'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Marquer toutes les notifications d'un agent comme lues
     */
    public function markAllAsReadForAgent($agentId)
    {
        return TontineNotification::unread()
            ->forAgent($agentId)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Obtenir le nombre de notifications non lues pour un agent
     */
    public function getUnreadCountForAgent($agentId)
    {
        return TontineNotification::unread()
            ->forAgent($agentId)
            ->count();
    }

    /**
     * Traitement automatique après un paiement validé
     */
    public function processPaymentValidated(Payment $payment)
    {
        $tontine = $payment->tontine;
        
        // Vérifier si la tontine est maintenant terminée
        $this->checkTontineCompletion($tontine);
        
        // Si terminée, créer un rappel de livraison après 1 jour
        if ($tontine->status === 'completed') {
            // Ici on pourrait programmer une tâche pour plus tard
            // Pour l'instant, on crée directement le rappel
            $this->createDeliveryReminder($tontine);
        }
    }
}
