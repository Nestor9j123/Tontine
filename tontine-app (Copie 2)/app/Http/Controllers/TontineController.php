<?php

namespace App\Http\Controllers;

use App\Models\Tontine;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\Request;

class TontineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tontine::with(['client', 'product', 'agent']);

        // Filtrer par agent si c'est un agent connecté
        if (auth()->user()->hasRole('agent')) {
            $query->where('agent_id', auth()->id());
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
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

        // Filtrer par produit
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
        // Support pour product_uuid (nouvelle méthode sécurisée)
        if ($request->filled('product_uuid')) {
            $product = \App\Models\Product::where('uuid', $request->product_uuid)->first();
            if ($product) {
                $query->where('product_id', $product->id);
            }
        }

        $perPage = $request->get('per_page', 15);
        $tontines = $query->latest()->paginate($perPage)->appends($request->except('page'));

        return view('tontines.index', compact('tontines'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Filtrer les clients selon le rôle
        if (auth()->user()->hasRole('agent')) {
            $clients = Client::byAgent(auth()->id())->active()->get();
        } else {
            $clients = Client::active()->get();
        }
        
        $products = Product::active()->get();
        $selectedClientId = $request->get('client_id');
        
        // Support pour client_uuid (nouvelle méthode sécurisée)
        if ($request->filled('client_uuid')) {
            $client = \App\Models\Client::where('uuid', $request->client_uuid)->first();
            if ($client) {
                $selectedClientId = $client->id;
            }
        }
        
        return view('tontines.create', compact('clients', 'products', 'selectedClientId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'product_id' => 'required|exists:products,id',
            'start_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $client = Client::findOrFail($validated['client_id']);
        
        // Vérifier que l'agent peut créer une tontine pour ce client
        if (auth()->user()->hasRole('agent') && $client->agent_id != auth()->id()) {
            return back()->withErrors([
                'client_id' => 'Vous ne pouvez créer une tontine que pour vos propres clients.'
            ])->withInput();
        }
        
        // Calculer automatiquement les montants et assigner l'agent du client
        $validated['agent_id'] = $client->agent_id;
        $validated['total_amount'] = $product->price;
        $validated['paid_amount'] = 0;
        $validated['remaining_amount'] = $product->price;
        
        // Calculer la date de fin et le nombre de paiements
        $validated['end_date'] = now()->parse($validated['start_date'])->addMonths($product->duration_months);
        
        // Calculer le nombre de paiements selon le type
        switch($product->type) {
            case 'daily':
                $validated['total_payments'] = $product->duration_months * 30; // Approximatif
                break;
            case 'weekly':
                $validated['total_payments'] = $product->duration_months * 4;
                break;
            case 'monthly':
                $validated['total_payments'] = $product->duration_months;
                break;
        }
        
        $validated['completed_payments'] = 0;
        $validated['status'] = 'active';

        $tontine = Tontine::create($validated);

        \App\Models\ActivityLog::log('create', 'Tontine', $tontine->id, null, $validated);

        return redirect()->route('tontines.show', $tontine)->with('success', 'Tontine créée avec succès !');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tontine $tontine)
    {
        $tontine->load(['client', 'product', 'agent', 'payments.collector']);
        return view('tontines.show', compact('tontine'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tontine $tontine)
    {
        $clients = Client::active()->get();
        $products = Product::active()->get();
        return view('tontines.edit', compact('tontine', 'clients', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tontine $tontine)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,completed,suspended,cancelled',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $tontine->toArray();
        $tontine->update($validated);

        \App\Models\ActivityLog::log('update', 'Tontine', $tontine->id, $oldValues, $validated);

        return redirect()->route('tontines.show', $tontine)->with('success', 'Tontine mise à jour !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tontine $tontine)
    {
        $oldValues = $tontine->toArray();
        $tontine->delete();

        \App\Models\ActivityLog::log('delete', 'Tontine', $tontine->id, $oldValues, null);

        return redirect()->route('tontines.index')->with('success', 'Tontine supprimée !');
    }

    /**
     * Validate a tontine (Secretary/Admin)
     */
    public function validateTontine(Tontine $tontine)
    {
        $tontine->update([
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);

        return back()->with('success', 'Tontine validée !');
    }
}
