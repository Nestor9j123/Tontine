<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Client::with(['agent', 'tontines']);

        // Filtrer par agent si c'est un agent connecté
        if (auth()->user()->hasRole('agent')) {
            $query->where('agent_id', auth()->id());
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filtrer par statut
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Filtrer par agent (pour admin/secrétaire)
        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }
        
        // Support pour agent_uuid (nouvelle méthode sécurisée)
        if ($request->filled('agent_uuid')) {
            $agent = \App\Models\User::findByUuid($request->agent_uuid);
            if ($agent) {
                $query->where('agent_id', $agent->id);
            }
        }

        $perPage = $request->get('per_page', 15);
        $clients = $query->latest()->paginate($perPage)->appends($request->except('page'));

        return view('clients.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = \App\Models\Product::active()->orderBy('name')->get();
        return view('clients.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Règles de validation différentes selon le rôle
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:clients,phone|regex:/^\+228[0-9]{8}$/|size:12',
            'phone_secondary' => 'nullable|string|regex:/^\+228[0-9]{8}$/|size:12',
            'email' => 'nullable|email|unique:clients,email',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'id_card_number' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            'has_existing_payments' => 'boolean',
            'existing_tontines' => 'nullable|required_if:has_existing_payments,1|array',
            'existing_tontines.*.product_id' => 'required_with:existing_tontines|exists:products,id',
            'existing_tontines.*.payments_count' => 'required_with:existing_tontines|integer|min:1|max:36',
            'existing_tontines.*.payments_amount' => 'required_with:existing_tontines|numeric|min:0',
            'existing_tontines.*.start_date' => 'required_with:existing_tontines|date',
            'existing_tontines.*.notes' => 'nullable|string|max:1000',
        ];
        
        // agent_id requis seulement pour admin/secrétaire
        if (!auth()->user()->hasRole('agent')) {
            $rules['agent_id'] = 'required|exists:users,id';
        }
        
        $validated = $request->validate($rules, [
            'phone.regex' => 'Le numéro de téléphone doit être au format +228 suivi de 8 chiffres.',
            'phone.size' => 'Le numéro de téléphone doit contenir exactement 8 chiffres après +228.',
            'phone_secondary.regex' => 'Le numéro de téléphone secondaire doit être au format +228 suivi de 8 chiffres.',
            'phone_secondary.size' => 'Le numéro de téléphone secondaire doit contenir exactement 8 chiffres après +228.',
        ]);

        // Si c'est un agent, forcer son ID
        if (auth()->user()->hasRole('agent')) {
            $validated['agent_id'] = auth()->id();
        }

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('clients', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $client = Client::create($validated);

        // Si le client a des paiements existants, créer les tontines et les paiements
        $tontinesCreated = 0;
        if ($request->has('has_existing_payments') && $request->has('existing_tontines')) {
            try {
                $tontinesCreated = $this->createExistingTontinesAndPayments($client, $validated);
            } catch (\Exception $e) {
                // En cas d'erreur, log et continuer avec un message d'avertissement
                \Log::error('Erreur lors de la création des tontines existantes: ' . $e->getMessage());
            }
        }

        // Logger l'activité
        \App\Models\ActivityLog::log('create', 'Client', $client->id, null, $validated);

        $message = 'Client créé avec succès !';
        if ($request->has('has_existing_payments') && $tontinesCreated > 0) {
            $message .= " {$tontinesCreated} tontine(s) et leurs paiements existants ont été enregistrés.";
        } elseif ($request->has('has_existing_payments') && $tontinesCreated === 0) {
            $message .= " Attention: Aucune tontine n'a pu être créée à partir des données fournies.";
        }

        return redirect()->route('clients.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $client->load(['agent', 'tontines.product', 'payments']);
        return view('clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        $products = \App\Models\Product::active()->orderBy('name')->get();
        return view('clients.edit', compact('client', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        // Règles de validation différentes selon le rôle
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|unique:clients,phone,' . $client->id . '|regex:/^\+228[0-9]{8}$/|size:12',
            'phone_secondary' => 'nullable|string|regex:/^\+228[0-9]{8}$/|size:12',
            'email' => 'nullable|email|unique:clients,email,' . $client->id,
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'id_card_number' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ];
        
        // agent_id requis seulement pour admin/secrétaire
        if (!auth()->user()->hasRole('agent')) {
            $rules['agent_id'] = 'required|exists:users,id';
        }
        
        $validated = $request->validate($rules, [
            'phone.regex' => 'Le numéro de téléphone doit être au format +228 suivi de 8 chiffres.',
            'phone.size' => 'Le numéro de téléphone doit contenir exactement 8 chiffres après +228.',
            'phone_secondary.regex' => 'Le numéro de téléphone secondaire doit être au format +228 suivi de 8 chiffres.',
            'phone_secondary.size' => 'Le numéro de téléphone secondaire doit contenir exactement 8 chiffres après +228.',
        ]);

        // Si c'est un agent, forcer son ID
        if (auth()->user()->hasRole('agent')) {
            $validated['agent_id'] = auth()->id();
        }

        // Gérer l'upload de la photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo
            if ($client->photo) {
                \Storage::disk('public')->delete($client->photo);
            }
            $validated['photo'] = $request->file('photo')->store('clients', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $oldValues = $client->toArray();
        $client->update($validated);

        // Logger l'activité
        \App\Models\ActivityLog::log('update', 'Client', $client->id, $oldValues, $validated);

        return redirect()->route('clients.show', $client)->with('success', 'Client mis à jour avec succès !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        // Supprimer la photo
        if ($client->photo) {
            \Storage::disk('public')->delete($client->photo);
        }

        $oldValues = $client->toArray();
        $client->delete();

        // Logger l'activité
        \App\Models\ActivityLog::log('delete', 'Client', $client->id, $oldValues, null);

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès !');
    }

    /**
     * Créer automatiquement les tontines et les paiements existants d'un client
     * @return int Nombre de tontines créées avec succès
     */
    private function createExistingTontinesAndPayments(Client $client, array $validated)
    {
        if (!isset($validated['existing_tontines']) || !is_array($validated['existing_tontines'])) {
            return 0;
        }

        $tontinesCreated = 0;
        foreach ($validated['existing_tontines'] as $tontineData) {
            $product = \App\Models\Product::find($tontineData['product_id']);
            
            if (!$product) {
                continue; // Passer à la tontine suivante
            }

            // Calculer la date de fin basée sur la durée du produit
            $startDate = new \Carbon\Carbon($tontineData['start_date']);
            $endDate = $startDate->copy()->addDays($product->duration_days);

            // Créer la tontine avec TOUS les champs requis
            $tontine = \App\Models\Tontine::create([
                'code' => 'TON-EX-' . strtoupper(uniqid()), // Code unique
                'client_id' => $client->id,
                'product_id' => $product->id,
                'agent_id' => $client->agent_id,
                'status' => 'active',
                'total_amount' => $product->price,
                'paid_amount' => $tontineData['payments_amount'],
                'remaining_amount' => $product->price - $tontineData['payments_amount'],
                'total_payments' => $tontineData['payments_count'],
                'completed_payments' => $tontineData['payments_count'], // Paiements déjà effectués
                'start_date' => $tontineData['start_date'],
                'end_date' => $endDate,
                'notes' => 'Tontine créée automatiquement lors de l\'ajout du client avec paiements existants. ' . ($tontineData['notes'] ?? ''),
            ]);

            // Créer les paiements existants pour cette tontine
            if ($tontineData['payments_count'] > 0 && $tontineData['payments_amount'] > 0) {
                $amountPerPayment = $tontineData['payments_amount'] / $tontineData['payments_count'];
                
                for ($i = 0; $i < $tontineData['payments_count']; $i++) {
                    \App\Models\Payment::create([
                        'reference' => 'PAY-EX-' . strtoupper(uniqid()), // Référence unique
                        'client_id' => $client->id,
                        'tontine_id' => $tontine->id,
                        'collected_by' => $client->agent_id, // Agent qui a collecté
                        'amount' => $amountPerPayment,
                        'payment_date' => $startDate->copy()->addDays($i * 30), // Approximation mensuelle
                        'payment_method' => 'cash', // Correct field name
                        'status' => 'validated', // Correct status value
                        'validated_by' => auth()->id(), // Qui valide l'import
                        'validated_at' => now(), // Date de validation
                        'notes' => 'Paiement existant importé automatiquement pour ' . $product->name,
                    ]);
                }
            }

            // Mettre à jour le statut de la tontine si complète
            if ($tontine->remaining_amount <= 0) {
                $tontine->update(['status' => 'completed']);
            }
            
            $tontinesCreated++;
        }
        
        return $tontinesCreated;
    }
}
