<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Client;
use App\Models\Tontine;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Liste paginée des paiements.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Payment::with([
            'client:id,uuid,first_name,last_name,phone',
            'tontine:id,uuid,code',
            'collector:id,uuid,name',
        ]);

        // Les agents ne voient que leurs paiements collectés
        if ($user->hasRole('agent')) {
            $query->where('collected_by', $user->id);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        if ($clientUuid = $request->get('client_uuid')) {
            $client = Client::where('uuid', $clientUuid)->first();
            if ($client) {
                $query->where('client_id', $client->id);
            }
        }

        if ($tontineUuid = $request->get('tontine_uuid')) {
            $tontine = Tontine::where('uuid', $tontineUuid)->first();
            if ($tontine) {
                $query->where('tontine_id', $tontine->id);
            }
        }

        if ($collectorUuid = $request->get('collector_uuid')) {
            $collector = User::where('uuid', $collectorUuid)->first();
            if ($collector) {
                $query->where('collected_by', $collector->id);
            }
        }

        $perPage = min((int) $request->get('per_page', 20), 100);
        $payments = $query->latest('payment_date')->paginate($perPage)->appends($request->except('page'));

        return response()->json([
            'data' => $payments->getCollection()->map(fn ($p) => $this->transformPayment($p)),
            'meta' => [
                'current_page' => $payments->currentPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'last_page' => $payments->lastPage(),
            ],
        ]);
    }

    /**
     * Détail d'un paiement.
     */
    public function show(Request $request, Payment $payment)
    {
        $user = $request->user();

        // L'agent ne peut voir qu'un paiement qu'il a collecté ou qui appartient à une de ses tontines
        if ($user->hasRole('agent')) {
            $payment->loadMissing('tontine.agent');
            $isCollector = $payment->collected_by === $user->id;
            $isTontineAgent = $payment->tontine && $payment->tontine->agent_id === $user->id;

            if (! $isCollector && ! $isTontineAgent) {
                return response()->json([
                    'message' => 'Accès refusé à ce paiement',
                ], 403);
            }
        }

        $payment->load([
            'client:id,uuid,first_name,last_name,phone',
            'tontine:id,uuid,code',
            'collector:id,uuid,name',
            'validator:id,uuid,name',
        ]);

        return response()->json([
            'data' => $this->transformPayment($payment, detailed: true),
        ]);
    }

    /**
     * Transformer un paiement pour la réponse API.
     */
    private function transformPayment(Payment $payment, bool $detailed = false): array
    {
        $base = [
            'id' => $payment->id,
            'uuid' => $payment->uuid,
            'reference' => $payment->reference,
            'amount' => $payment->amount,
            'status' => $payment->status,
            'payment_date' => optional($payment->payment_date)->toDateString(),
            'payment_method' => $payment->payment_method,
            'is_multiple_payment' => (bool) $payment->is_multiple_payment,
            'client' => $payment->client ? [
                'uuid' => $payment->client->uuid,
                'full_name' => $payment->client->full_name,
                'phone' => $payment->client->phone,
            ] : null,
            'tontine' => $payment->tontine ? [
                'uuid' => $payment->tontine->uuid,
                'code' => $payment->tontine->code,
            ] : null,
            'collector' => $payment->collector ? [
                'uuid' => $payment->collector->uuid,
                'name' => $payment->collector->name,
            ] : null,
        ];

        if (! $detailed) {
            return $base;
        }

        return array_merge($base, [
            'daily_amount' => $payment->daily_amount,
            'days_count' => $payment->days_count,
            'transaction_id' => $payment->transaction_id,
            'notes' => $payment->notes,
            'rejection_reason' => $payment->rejection_reason,
            'validated_at' => optional($payment->validated_at)->toISOString(),
            'validated_by' => $payment->validator ? [
                'uuid' => $payment->validator->uuid,
                'name' => $payment->validator->name,
            ] : null,
        ]);
    }
}
