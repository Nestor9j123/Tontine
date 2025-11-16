<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\NotebookPayment;
use Illuminate\Http\Request;

class NotebookController extends Controller
{
    /**
     * Afficher le carnet numérique d'un client
     */
    public function show(Client $client)
    {
        $client->load(['tontines.product', 'tontines.payments', 'payments', 'notebookPayments.user']);
        
        return view('notebooks.show', compact('client'));
    }
    
    /**
     * Enregistrer un paiement de carnet physique
     */
    public function payNotebook(Request $request, Client $client)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:300',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
        
        // Vérifier que le montant ne dépasse pas ce qui reste à payer
        $remaining = 300 - $client->notebook_amount_paid;
        if ($validated['amount'] > $remaining) {
            return back()->withErrors(['amount' => "Le montant ne peut pas dépasser {$remaining} FCFA (reste à payer)"]);
        }
        
        // Créer le paiement
        NotebookPayment::create([
            'client_id' => $client->id,
            'user_id' => auth()->id(),
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'],
        ]);
        
        // Mettre à jour le client
        $newTotal = $client->notebook_amount_paid + $validated['amount'];
        $client->update([
            'notebook_amount_paid' => $newTotal,
            'notebook_fully_paid' => $newTotal >= 300,
            'has_physical_notebook' => true,
        ]);
        
        return back()->with('success', 'Paiement de carnet enregistré!');
    }
}
