<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Liste paginée des clients pour l'API.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Client::with(['agent:id,uuid,name,email'])
            ->withCount(['tontines', 'payments']);

        // Filtrer par agent si l'utilisateur est un agent
        if ($user->hasRole('agent')) {
            $query->where('agent_id', $user->id);
        }

        // Recherche texte
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                    ->orWhere('last_name', 'ilike', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Statut actif / inactif
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $perPage = min((int) $request->get('per_page', 20), 100);
        $clients = $query->latest()->paginate($perPage)->appends($request->except('page'));

        return response()->json([
            'data' => $clients->getCollection()->map(fn ($client) => $this->transformClient($client)),
            'meta' => [
                'current_page' => $clients->currentPage(),
                'per_page' => $clients->perPage(),
                'total' => $clients->total(),
                'last_page' => $clients->lastPage(),
            ],
        ]);
    }

    /**
     * Détail d'un client.
     */
    public function show(Request $request, Client $client)
    {
        $user = $request->user();

        if ($user->hasRole('agent') && $client->agent_id !== $user->id) {
            return response()->json([
                'message' => 'Accès refusé à ce client',
            ], 403);
        }

        $client->load(['agent:id,uuid,name,email', 'tontines', 'payments']);

        return response()->json([
            'data' => $this->transformClient($client, detailed: true),
        ]);
    }

    /**
     * Transformer un client pour la réponse API.
     */
    private function transformClient(Client $client, bool $detailed = false): array
    {
        $base = [
            'id' => $client->id,
            'uuid' => $client->uuid,
            'code' => $client->code,
            'first_name' => $client->first_name,
            'last_name' => $client->last_name,
            'full_name' => $client->full_name,
            'phone' => $client->phone,
            'city' => $client->city,
            'is_active' => (bool) $client->is_active,
            'agent' => $client->agent ? [
                'uuid' => $client->agent->uuid,
                'name' => $client->agent->name,
                'email' => $client->agent->email,
            ] : null,
            'tontines_count' => $client->tontines_count ?? null,
            'payments_count' => $client->payments_count ?? null,
            'created_at' => optional($client->created_at)->toISOString(),
        ];

        if (! $detailed) {
            return $base;
        }

        return array_merge($base, [
            'address' => $client->address,
            'email' => $client->email,
            'tontines' => $client->tontines->map(function ($t) {
                return [
                    'id' => $t->id,
                    'uuid' => $t->uuid,
                    'code' => $t->code,
                    'status' => $t->status,
                    'total_amount' => $t->total_amount,
                    'paid_amount' => $t->paid_amount,
                    'remaining_amount' => $t->remaining_amount,
                ];
            }),
            'payments' => $client->payments->map(function ($p) {
                return [
                    'id' => $p->id,
                    'reference' => $p->reference,
                    'amount' => $p->amount,
                    'status' => $p->status,
                    'payment_date' => optional($p->payment_date)->toDateString(),
                ];
            }),
        ]);
    }
}
