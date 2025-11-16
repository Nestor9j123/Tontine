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
        return view('clients.create');
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
            'phone' => 'required|string|unique:clients,phone',
            'phone_secondary' => 'nullable|string',
            'email' => 'nullable|email|unique:clients,email',
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
        
        $validated = $request->validate($rules);

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

        // Logger l'activité
        \App\Models\ActivityLog::log('create', 'Client', $client->id, null, $validated);

        return redirect()->route('clients.index')->with('success', 'Client créé avec succès !');
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
        return view('clients.edit', compact('client'));
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
            'phone' => 'required|string|unique:clients,phone,' . $client->id,
            'phone_secondary' => 'nullable|string',
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
        
        $validated = $request->validate($rules);

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
}
