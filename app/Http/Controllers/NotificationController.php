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
            // Admin/Secretary voient toutes les notifications
            $query = TontineNotification::query();
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
            }
        }

        $notifications = $query->with(['tontine.product', 'client', 'agent'])
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->except('page'));

        // Statistiques
        if ($user->hasRole('agent')) {
            $totalQuery = TontineNotification::where(function($q) use ($user) {
                $q->where('agent_id', $user->id)->orWhereNull('agent_id');
            });
            $unreadQuery = TontineNotification::unread()->where(function($q) use ($user) {
                $q->where('agent_id', $user->id)->orWhereNull('agent_id');
            });
            
            $stats = [
                'total' => $totalQuery->count(),
                'unread' => $unreadQuery->count(),
            ];
        } else {
            $stats = [
                'total' => TontineNotification::count(),
                'unread' => TontineNotification::unread()->count(),
            ];
        }

        return view('notifications.index', compact('notifications', 'stats'));
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
     * Supprimer une notification
     */
    public function destroy(TontineNotification $notification)
    {
        // Vérifier les permissions
        if (auth()->user()->hasRole('agent') && $notification->agent_id !== auth()->id()) {
            abort(403);
        }

        $notification->delete();

        return redirect()->route('notifications.index')
            ->with('success', 'Notification supprimée !');
    }
}
