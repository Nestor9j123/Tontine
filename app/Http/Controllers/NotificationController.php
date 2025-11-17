<?php

namespace App\Http\Controllers;

use App\Models\TontineNotification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;
    }

    /**
     * Afficher les notifications de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Filtrer selon le rôle
        if ($user->hasRole('agent')) {
            $query = TontineNotification::where(function($q) use ($user) {
                $q->where('agent_id', $user->id)
                  ->orWhereNull('agent_id'); // Notifications générales
            });
        } else {
            // Admin/Secretary voient toutes les notifications (y compris supprimées)
            $query = TontineNotification::withTrashed();
        }

        // Filtres
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->unread();
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            } elseif ($request->status === 'delivered') {
                $query->where('is_delivered', true);
            } elseif ($request->status === 'deleted') {
                $query->onlyTrashed();
            }
        }

        // Filtre par agent (pour admin/secretary)
        if ($request->filled('agent_id') && !$user->hasRole('agent')) {
            $query->where('agent_id', $request->agent_id);
        }

        $notifications = $query->with([
                'tontine.product', 
                'client', 
                'agent', 
                'deletedBy', 
                'markedDeliveredBy'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->except('page'));

        // Statistiques
        if ($user->hasRole('agent')) {
            $baseQuery = TontineNotification::where(function($q) use ($user) {
                $q->where('agent_id', $user->id)->orWhereNull('agent_id');
            });
            
            $stats = [
                'total' => $baseQuery->count(),
                'unread' => $baseQuery->unread()->count(),
                'delivered' => $baseQuery->where('is_delivered', true)->count(),
            ];
        } else {
            $stats = [
                'total' => TontineNotification::count(),
                'unread' => TontineNotification::unread()->count(),
                'delivered' => TontineNotification::where('is_delivered', true)->count(),
                'deleted' => TontineNotification::onlyTrashed()->count(),
            ];
        }

        // Liste des agents pour le filtre (admin/secretary seulement)
        $agents = $user->hasRole('agent') ? collect() : 
            \App\Models\User::role('agent')->orderBy('name')->get();

        return view('notifications.index', compact('notifications', 'stats', 'agents'));
    }

    /**
     * Afficher une notification spécifique
     */
    public function show(TontineNotification $notification)
    {
        // Vérifier les permissions
        $user = auth()->user();
        if ($user->hasRole('agent') && $notification->agent_id !== $user->id && $notification->agent_id !== null) {
            abort(403);
        }

        $notification->load(['tontine.product', 'client', 'agent', 'deletedBy', 'markedDeliveredBy']);

        return view('notifications.show', compact('notification'));
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead(TontineNotification $notification)
    {
        // Vérifier les permissions
        if (auth()->user()->hasRole('agent') && $notification->agent_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        
        if ($user->hasRole('agent')) {
            $this->notificationService->markAllAsReadForAgent($user->id);
        } else {
            TontineNotification::unread()->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Obtenir le nombre de notifications non lues (API)
     */
    public function getUnreadCount()
    {
        $user = auth()->user();
        
        $count = $user->hasRole('agent')
            ? $this->notificationService->getUnreadCountForAgent($user->id)
            : TontineNotification::unread()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Obtenir les notifications récentes (API)
     */
    public function getRecent()
    {
        $user = auth()->user();
        
        $query = $user->hasRole('agent')
            ? TontineNotification::forAgent($user->id)
            : TontineNotification::query();

        $notifications = $query->with(['tontine.product', 'client'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($notifications);
    }

    /**
     * Marquer une notification comme livrée (et décrémenter le stock si c'est une tontine)
     */
    public function markAsDelivered(TontineNotification $notification)
    {
        // Vérifier les permissions
        if (!$notification->canBeMarkedAsDeliveredBy(auth()->user())) {
            abort(403, 'Vous n\'avez pas les permissions pour marquer cette notification comme livrée.');
        }

        try {
            // Si c'est une notification de paiement terminé avec tontine, marquer la tontine comme livrée aussi
            if ($notification->type === 'payment_completed' && $notification->tontine) {
                // Vérifier si la tontine peut être livrée
                if (!$notification->tontine->canBeDelivered()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cette tontine ne peut pas être livrée (déjà livrée, non terminée ou stock insuffisant).'
                    ], 400);
                }

                // Marquer la tontine comme livrée (ceci décrémente automatiquement le stock)
                $notification->tontine->markAsDelivered(auth()->id());
            }

            // Marquer la notification comme livrée
            $notification->markAsDelivered(auth()->id());

            $message = 'Notification marquée comme livrée !';
            if ($notification->tontine) {
                $message .= ' Le stock du produit a été automatiquement décrémenté.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'marked_by' => auth()->user()->name,
                'marked_at' => $notification->marked_delivered_at->format('d/m/Y H:i'),
                'tontine_delivered' => $notification->tontine ? $notification->tontine->isDelivered() : false
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la livraison : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une notification (soft delete avec traçabilité)
     */
    public function destroy(TontineNotification $notification)
    {
        // Vérifier les permissions
        if (!$notification->canBeDeletedBy(auth()->user())) {
            abort(403, 'Seuls les super admins et secrétaires peuvent supprimer les notifications.');
        }

        // Soft delete avec traçabilité
        $notification->update(['deleted_by' => auth()->id()]);
        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification supprimée avec succès !');
    }

    /**
     * Restaurer une notification supprimée
     */
    public function restore($uuid)
    {
        $notification = TontineNotification::withTrashed()->where('uuid', $uuid)->firstOrFail();
        
        // Vérifier les permissions
        if (!$notification->canBeDeletedBy(auth()->user())) {
            abort(403, 'Seuls les super admins et secrétaires peuvent restaurer les notifications.');
        }

        $notification->restore();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification restaurée avec succès !');
    }

    /**
     * Supprimer définitivement une notification
     */
    public function forceDelete($uuid)
    {
        $notification = TontineNotification::withTrashed()->where('uuid', $uuid)->firstOrFail();
        
        // Vérifier les permissions (seuls les super admins)
        if (!auth()->user()->hasRole('super_admin')) {
            abort(403, 'Seuls les super admins peuvent supprimer définitivement les notifications.');
        }

        $notification->forceDelete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification supprimée définitivement !');
    }
}
