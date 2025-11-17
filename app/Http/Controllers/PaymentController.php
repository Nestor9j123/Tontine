<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Tontine;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['client', 'tontine.product', 'collector']);

        // Filtrer par agent si c'est un agent connecté
        if (auth()->user()->hasRole('agent')) {
            $query->where('collected_by', auth()->id());
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtrer par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtrer par méthode
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filtrer par date
        if ($request->filled('date')) {
            $query->whereDate('payment_date', $request->date);
        }

        // Filtrer par collecteur (agent) - ancienne méthode avec ID
        if ($request->filled('collector_id')) {
            $query->where('collected_by', $request->collector_id);
        }
        
        // Filtrer par collecteur (agent) - nouvelle méthode sécurisée avec UUID
        if ($request->filled('collector_uuid')) {
            $collector = \App\Models\User::findByUuid($request->collector_uuid);
            if ($collector) {
                $query->where('collected_by', $collector->id);
            }
        }

        $perPage = $request->get('per_page', 15);
        $payments = $query->latest()->paginate($perPage)->appends($request->except('page'));

        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('payments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'tontine_id' => 'required|exists:tontines,id',
            'amount' => 'required|numeric|min:1',
            'daily_amount' => 'nullable|numeric|min:1',
            'days_count' => 'nullable|integer|min:1|max:365',
            'payment_date' => 'nullable|date',
            'payment_method' => 'required|in:cash,mobile_money,bank_transfer',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Vérifier que le montant ne dépasse pas le restant à payer
        $tontine = Tontine::find($validated['tontine_id']);
        if ($validated['amount'] > $tontine->remaining_amount) {
            return back()->withErrors([
                'amount' => "Le montant ne peut pas dépasser le restant à payer ({$tontine->remaining_amount} FCFA)"
            ])->withInput();
        }
        
        // Vérifier que l'agent peut créer un paiement pour cette tontine
        if (auth()->user()->hasRole('agent') && $tontine->agent_id != auth()->id()) {
            return back()->withErrors([
                'tontine_id' => 'Vous ne pouvez enregistrer un paiement que pour vos propres tontines.'
            ])->withInput();
        }

        $validated['collected_by'] = auth()->id();
        
        // Forcer la date de paiement à aujourd'hui pour sécurité
        $validated['payment_date'] = now()->format('Y-m-d');
        
        // Détecter si c'est un paiement multiple
        $isMultiplePayment = !empty($validated['daily_amount']) && !empty($validated['days_count']);
        $validated['is_multiple_payment'] = $isMultiplePayment;
        
        // Vérifier la cohérence pour les paiements multiples
        if ($isMultiplePayment) {
            $expectedAmount = $validated['daily_amount'] * $validated['days_count'];
            if (abs($validated['amount'] - $expectedAmount) > 0.01) {
                return back()->withErrors([
                    'amount' => 'Le montant total ne correspond pas au calcul (montant quotidien × nombre de jours)'
                ])->withInput();
            }
        }
        
        // Logique de validation automatique pour les agents
        if (auth()->user()->hasRole('agent') && $validated['amount'] <= 100000) {
            // Auto-validation pour les agents avec montants ≤ 100k
            $validated['status'] = 'validated';
            $validated['validated_by'] = auth()->id();
            $validated['validated_at'] = now();
        } else {
            // En attente pour les montants > 100k ou autres rôles
            $validated['status'] = 'pending';
        }

        $payment = Payment::create($validated);

        // Mettre à jour la tontine
        $tontine->increment('completed_payments');
        $tontine->increment('paid_amount', $validated['amount']);
        $tontine->decrement('remaining_amount', $validated['amount']);
        
        // Vérifier si la tontine est maintenant complète
        $tontine->refresh(); // Récupérer les valeurs mises à jour
        if ($tontine->remaining_amount <= 0 && $tontine->status !== 'completed') {
            $tontine->update([
                'status' => 'completed',
                'end_date' => now(),
                'validated_at' => now(),
                'validated_by' => auth()->id(),
            ]);
        }

        \App\Models\ActivityLog::log('create', 'Payment', $payment->id, null, $validated);

        // Messages de succès adaptés
        if (auth()->user()->hasRole('agent')) {
            $baseMessage = $payment->status === 'validated' 
                ? 'Paiement validé automatiquement (≤100k)' 
                : 'Paiement enregistré en attente de validation (>100k)';
            
            if ($payment->is_multiple_payment) {
                $message = $baseMessage . sprintf(' - %d jours × %s FCFA = %s FCFA. Montrez le reçu au client.',
                    $payment->days_count,
                    number_format($payment->daily_amount, 0, ',', ' '),
                    number_format($payment->amount, 0, ',', ' ')
                );
            } else {
                $message = $baseMessage . '! Montrez le reçu au client.';
            }
            
            return redirect()->route('payments.show', $payment)->with('success', $message);
        }
        
        $message = 'Paiement enregistré avec succès !';
        if ($payment->is_multiple_payment) {
            $message .= sprintf(' (%d jours × %s FCFA)',
                $payment->days_count,
                number_format($payment->daily_amount, 0, ',', ' ')
            );
        }
        
        return redirect()->route('payments.show', $payment)->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['client', 'tontine.product', 'collector', 'validator']);
        return view('payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        return view('payments.edit', compact('payment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);

        return redirect()->route('payments.show', $payment)->with('success', 'Paiement mis à jour !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        // Mettre à jour la tontine
        $tontine = $payment->tontine;
        $tontine->decrement('completed_payments');
        $tontine->decrement('paid_amount', $payment->amount);
        $tontine->increment('remaining_amount', $payment->amount);

        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Paiement supprimé !');
    }

    /**
     * Validate a payment (Agent/Secretary/Admin)
     */
    public function validatePayment(Payment $payment)
    {
        // Vérifier les permissions
        if (auth()->user()->hasRole('agent')) {
            // Les agents ne peuvent valider que les paiements ≤ 100 000 FCFA
            if ($payment->amount > 100000) {
                return back()->withErrors(['error' => 'Les agents ne peuvent valider que les paiements de 100 000 FCFA maximum.']);
            }
        } elseif (!auth()->user()->hasRole('secretary') && !auth()->user()->hasRole('super_admin')) {
            return back()->withErrors(['error' => 'Vous n\'avez pas l\'autorisation de valider ce paiement.']);
        }

        $payment->update([
            'status' => 'validated',
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        // Si le paiement n'était pas encore validé automatiquement, mettre à jour la tontine
        $tontine = $payment->tontine;
        if ($payment->wasChanged('status')) {
            // Vérifier si la tontine est maintenant complète
            $tontine->refresh();
            if ($tontine->remaining_amount <= 0 && $tontine->status !== 'completed') {
                $tontine->update([
                    'status' => 'completed',
                    'end_date' => now(),
                    'validated_at' => now(),
                    'validated_by' => auth()->id(),
                ]);
            }
        }

        // Traitement automatique des notifications
        $notificationService = new NotificationService();
        $notificationService->processPaymentValidated($payment);

        $message = auth()->user()->hasRole('agent') ? 
            'Paiement validé automatiquement (≤ 100k FCFA) !' : 
            'Paiement validé !';

        return back()->with('success', $message);
    }

    /**
     * Reject a payment (Secretary/Admin)
     */
    public function reject(Request $request, Payment $payment)
    {
        // Vérifier que le paiement peut être rejeté
        if ($payment->status === 'rejected') {
            return back()->with('error', 'Ce paiement est déjà rejeté.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        try {
            // Sauvegarder l'ancien statut avant la mise à jour
            $oldStatus = $payment->status;
            
            $payment->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);

            // Annuler la mise à jour de la tontine seulement si le paiement était validé
            if ($oldStatus === 'validated') {
                $tontine = $payment->tontine;
                if ($tontine) {
                    $tontine->decrement('completed_payments');
                    $tontine->decrement('paid_amount', $payment->amount);
                    $tontine->increment('remaining_amount', $payment->amount);
                }
            }

            return back()->with('success', 'Paiement rejeté !');
        } catch (\Exception $e) {
            \Log::error('Erreur lors du rejet du paiement: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du rejet du paiement.');
        }
    }
}
