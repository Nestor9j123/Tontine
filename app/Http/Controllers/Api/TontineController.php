<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tontine;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;

class TontineController extends Controller
{
    /**
     * Liste paginée des tontines.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Tontine::with([
            'client:id,uuid,first_name,last_name,phone',
            'product:id,uuid,name,price',
            'agent:id,uuid,name',
        ])->withCount('payments');

        // Les agents ne voient que leurs tontines
        if ($user->hasRole('agent')) {
            $query->where('agent_id', $user->id);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'ilike', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('first_name', 'ilike', "%{$search}%")
                            ->orWhere('last_name', 'ilike', "%{$search}%");
                    });
            });
        }

        if ($clientUuid = $request->get('client_uuid')) {
            $client = Client::where('uuid', $clientUuid)->first();
            if ($client) {
                $query->where('client_id', $client->id);
            }
        }

        if ($productUuid = $request->get('product_uuid')) {
            $product = Product::where('uuid', $productUuid)->first();
            if ($product) {
                $query->where('product_id', $product->id);
            }
        }

        $perPage = min((int) $request->get('per_page', 20), 100);
        $tontines = $query->latest()->paginate($perPage)->appends($request->except('page'));

        return response()->json([
            'data' => $tontines->getCollection()->map(fn ($t) => $this->transformTontine($t)),
            'meta' => [
                'current_page' => $tontines->currentPage(),
                'per_page' => $tontines->perPage(),
                'total' => $tontines->total(),
                'last_page' => $tontines->lastPage(),
            ],
        ]);
    }

    /**
     * Détail d'une tontine.
     */
    public function show(Request $request, Tontine $tontine)
    {
        $user = $request->user();

        if ($user->hasRole('agent') && $tontine->agent_id !== $user->id) {
            return response()->json([
                'message' => 'Accès refusé à cette tontine',
            ], 403);
        }

        $tontine->load([
            'client:id,uuid,first_name,last_name,phone',
            'product:id,uuid,name,price',
            'agent:id,uuid,name',
            'payments' => function ($q) {
                $q->orderBy('payment_date', 'desc');
            },
        ]);

        return response()->json([
            'data' => $this->transformTontine($tontine, detailed: true),
        ]);
    }

    /**
     * Transformer une tontine pour la réponse API.
     */
    private function transformTontine(Tontine $tontine, bool $detailed = false): array
    {
        $base = [
            'id' => $tontine->id,
            'uuid' => $tontine->uuid,
            'code' => $tontine->code,
            'status' => $tontine->status,
            'total_amount' => $tontine->total_amount,
            'paid_amount' => $tontine->paid_amount,
            'remaining_amount' => $tontine->remaining_amount,
            'total_payments' => $tontine->total_payments,
            'completed_payments' => $tontine->completed_payments,
            'progress_percentage' => $tontine->progress_percentage,
            'start_date' => optional($tontine->start_date)->toDateString(),
            'end_date' => optional($tontine->end_date)->toDateString(),
            'client' => $tontine->client ? [
                'uuid' => $tontine->client->uuid,
                'full_name' => $tontine->client->full_name,
                'phone' => $tontine->client->phone,
            ] : null,
            'product' => $tontine->product ? [
                'uuid' => $tontine->product->uuid,
                'name' => $tontine->product->name,
                'price' => $tontine->product->price,
            ] : null,
            'agent' => $tontine->agent ? [
                'uuid' => $tontine->agent->uuid,
                'name' => $tontine->agent->name,
            ] : null,
            'payments_count' => $tontine->payments_count ?? $tontine->payments()->count(),
        ];

        if (! $detailed) {
            return $base;
        }

        return array_merge($base, [
            'notes' => $tontine->notes,
            'payments' => $tontine->payments->map(fn ($p) => [
                'uuid' => $p->uuid,
                'reference' => $p->reference,
                'amount' => $p->amount,
                'status' => $p->status,
                'payment_date' => optional($p->payment_date)->toDateString(),
            ]),
        ]);
    }
}
