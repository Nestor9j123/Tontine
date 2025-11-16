<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Afficher l'interface de chat
     */
    public function index()
    {
        $user = auth()->user();
        
        // Récupérer les conversations de l'utilisateur
        $conversations = $user->conversations()
            ->with(['users', 'latestMessage.user'])
            ->get()
            ->map(function ($conversation) use ($user) {
                $conversation->unread_count = $conversation->getUnreadCount($user);
                $conversation->title = $this->getConversationTitle($conversation, $user);
                $conversation->avatar = $this->getConversationAvatar($conversation, $user);
                
                // Ajouter les informations de lecture pour le dernier message
                if ($conversation->latestMessage) {
                    $conversation->latestMessage->user_id = $conversation->latestMessage->user_id;
                    $conversation->latestMessage->is_read = $this->isMessageReadByOthers($conversation->latestMessage, $user);
                }
                
                return $conversation;
            });

        // Récupérer les utilisateurs avec qui on peut discuter
        $availableUsers = $this->getAvailableUsers($user);

        return view('chat.index', compact('conversations', 'availableUsers'));
    }

    /**
     * Récupérer les messages d'une conversation
     */
    public function getMessages(Conversation $conversation)
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur fait partie de la conversation
        if (!$conversation->isParticipant($user)) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Marquer comme lu
        $conversation->markAsRead($user);

        // Récupérer les messages
        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'uuid' => $message->uuid,
                    'content' => $message->content,
                    'type' => $message->type,
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        'avatar' => $this->getAvatarUrl($message->user),
                    ],
                    'is_own' => $message->user_id === $user->id,
                    'is_read' => $this->isMessageReadByOthers($message, $user),
                    'created_at' => $message->created_at->format('H:i'),
                    'created_at_full' => $message->created_at->format('d/m/Y H:i'),
                    'time_ago' => $message->time_ago,
                ];
            });

        return response()->json([
            'messages' => $messages,
            'conversation' => [
                'id' => $conversation->id,
                'uuid' => $conversation->uuid,
                'title' => $this->getConversationTitle($conversation, $user),
            ]
        ]);
    }

    /**
     * Envoyer un message
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur fait partie de la conversation
        if (!$conversation->isParticipant($user)) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'type' => 'in:text,file,image',
        ]);

        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'content' => $request->content,
            'type' => $request->type ?? 'text',
        ]);

        $message->load('user');

        // Ici on pourrait ajouter l'événement WebSocket
        // broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => [
                'id' => $message->id,
                'uuid' => $message->uuid,
                'content' => $message->content,
                'type' => $message->type,
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'avatar' => $this->getAvatarUrl($message->user),
                ],
                'is_own' => true,
                'is_read' => false, // Nouveau message, pas encore lu
                'created_at' => $message->created_at->format('H:i'),
                'created_at_full' => $message->created_at->format('d/m/Y H:i'),
                'created_at_iso' => $message->created_at->toISOString(),
                'time_ago' => $message->time_ago,
            ],
            'conversation_updated_at' => $message->created_at->toISOString()
        ]);
    }

    /**
     * Créer ou récupérer une conversation avec un utilisateur
     */
    public function startConversation(Request $request)
    {
        try {
            $user = auth()->user();
            
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $targetUser = User::findOrFail($request->user_id);

            // Vérifier les permissions
            if (!$user->canMessageUser($targetUser)) {
                return response()->json(['error' => 'Vous ne pouvez pas envoyer de message à cet utilisateur'], 403);
            }

            // Chercher une conversation existante
            $conversation = $user->getConversationWith($targetUser);

            if (!$conversation) {
                // Créer une nouvelle conversation
                DB::transaction(function () use (&$conversation, $user, $targetUser) {
                    $conversation = Conversation::create([
                        'type' => 'private',
                        'created_by' => $user->id,
                    ]);

                    $conversation->addParticipant($user);
                    $conversation->addParticipant($targetUser);
                });
            }

            return response()->json([
                'conversation' => [
                    'id' => $conversation->id,
                    'uuid' => $conversation->uuid,
                    'title' => $this->getConversationTitle($conversation, $user),
                    'avatar' => $this->getConversationAvatar($conversation, $user),
                    'unread_count' => $conversation->getUnreadCount($user),
                    'latest_message' => null,
                    'last_message_at' => $conversation->created_at->toISOString(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de conversation: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création de la conversation: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Récupérer les utilisateurs disponibles pour une conversation
     */
    private function getAvailableUsers(User $user)
    {
        $query = User::where('id', '!=', $user->id)->where('is_active', true);

        if ($user->hasRole('super_admin')) {
            // Super admin peut parler à tout le monde
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', ['agent', 'secretary']);
            });
        } elseif ($user->hasRole('secretary')) {
            // Secrétaire peut parler aux agents et super admin
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', ['agent', 'super_admin']);
            });
        } elseif ($user->hasRole('agent')) {
            // Agent peut parler aux secrétaires et super admin
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', ['secretary', 'super_admin']);
            });
        }

        return $query->with('roles')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()->name ?? 'user',
                'avatar' => $this->getAvatarUrl($user),
                'is_online' => false, // À implémenter avec WebSockets
            ];
        });
    }

    /**
     * Obtenir le titre d'une conversation
     */
    private function getConversationTitle(Conversation $conversation, User $user)
    {
        if ($conversation->title) {
            return $conversation->title;
        }

        // Pour les conversations privées, utiliser le nom de l'autre participant
        $otherUser = $conversation->users()->where('user_id', '!=', $user->id)->first();
        return $otherUser ? $otherUser->name : 'Conversation';
    }

    /**
     * Obtenir l'avatar d'une conversation
     */
    private function getConversationAvatar(Conversation $conversation, User $user)
    {
        if ($conversation->type === 'private') {
            // Pour les conversations privées, utiliser l'avatar de l'autre participant
            $otherUser = $conversation->users()->where('user_id', '!=', $user->id)->first();
            if ($otherUser) {
                return $this->getAvatarUrl($otherUser);
            }
        }
        
        // Avatar par défaut pour les groupes ou conversations sans participants
        return 'https://ui-avatars.com/api/?name=' . urlencode($conversation->title ?: 'Chat') . '&background=random&size=40';
    }

    /**
     * Mettre à jour le statut de lecture des messages
     */
    public function updateReadStatus(Conversation $conversation)
    {
        $user = auth()->user();
        
        // Vérifier que l'utilisateur fait partie de la conversation
        if (!$conversation->isParticipant($user)) {
            return response()->json(['error' => 'Accès non autorisé'], 403);
        }

        // Marquer la conversation comme lue
        $conversation->markAsRead($user);

        // Retourner les informations mises à jour pour toutes les conversations
        // où l'utilisateur actuel a envoyé le dernier message
        $updatedConversations = $user->conversations()
            ->with(['latestMessage.user'])
            ->get()
            ->filter(function ($conv) use ($user) {
                return $conv->latestMessage && $conv->latestMessage->user_id === $user->id;
            })
            ->map(function ($conv) use ($user) {
                return [
                    'uuid' => $conv->uuid,
                    'latest_message_is_read' => $this->isMessageReadByOthers($conv->latestMessage, $user)
                ];
            });

        return response()->json([
            'updated_conversations' => $updatedConversations
        ]);
    }

    /**
     * Vérifier si un message a été lu par les autres participants
     */
    private function isMessageReadByOthers(Message $message, User $currentUser)
    {
        // Si ce n'est pas notre message, on ne peut pas savoir s'il a été lu
        if ($message->user_id !== $currentUser->id) {
            return false;
        }

        // Pour les conversations privées, vérifier si l'autre participant a lu le message
        $conversation = $message->conversation;
        $otherParticipants = $conversation->participants()
            ->where('user_id', '!=', $currentUser->id)
            ->get();

        foreach ($otherParticipants as $participant) {
            // Si le message a été créé après la dernière lecture du participant, il n'est pas lu
            if (!$participant->last_read_at || $message->created_at > $participant->last_read_at) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtenir l'URL de l'avatar d'un utilisateur
     */
    private function getAvatarUrl(User $user)
    {
        // Pour l'instant, utiliser des avatars générés
        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random&size=40';
    }
}
