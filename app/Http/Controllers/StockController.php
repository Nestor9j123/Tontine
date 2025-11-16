<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Afficher la vue principale de gestion de stock
     */
    public function index(Request $request)
    {
        $query = Product::with('stockMovements');
        
        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('low_stock')) {
            $query->whereRaw('stock_quantity <= min_stock_alert');
        }
        
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage)->appends($request->except('page'));
        
        return view('stock.index', compact('products'));
    }
    
    /**
     * Afficher l'historique des mouvements de stock
     */
    public function movements(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);
        
        // Filtres
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        $perPage = $request->get('per_page', 15);
        $movements = $query->latest()->paginate($perPage)->appends($request->except('page'));
        $products = Product::all();
        
        return view('stock.movements', compact('movements', 'products'));
    }
    
    /**
     * Formulaire d'ajout de stock
     */
    public function create()
    {
        $products = Product::all();
        return view('stock.create', compact('products'));
    }
    
    /**
     * Enregistrer un mouvement de stock (entrée)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'reason' => 'required|string',
        ]);
        
        $product = Product::findOrFail($validated['product_id']);
        $stockBefore = $product->stock_quantity;
        
        // Calculer le nouveau stock selon le type
        if ($validated['type'] === 'in') {
            $stockAfter = $stockBefore + $validated['quantity'];
        } elseif ($validated['type'] === 'out') {
            if ($stockBefore < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'Stock insuffisant!'])->withInput();
            }
            $stockAfter = $stockBefore - $validated['quantity'];
        } else { // adjustment
            $stockAfter = $validated['quantity'];
        }
        
        // Créer le mouvement
        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'quantity' => $validated['type'] === 'adjustment' ? ($stockAfter - $stockBefore) : $validated['quantity'],
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'reference' => $validated['reference'],
            'reason' => $validated['reason'],
        ]);
        
        // Mettre à jour le stock du produit
        $product->update(['stock_quantity' => $stockAfter]);
        
        return redirect()->route('stock.index')->with('success', 'Mouvement de stock enregistré!');
    }
    
    /**
     * Marquer une tontine comme livrée et décrémenter le stock
     */
    public function deliverTontine(Request $request, $tontineId)
    {
        $tontine = \App\Models\Tontine::with('product')->findOrFail($tontineId);
        
        // Vérifier que la tontine est terminée
        if ($tontine->status !== 'completed') {
            return back()->withErrors(['error' => 'La tontine doit être terminée avant la livraison!']);
        }
        
        // Vérifier que pas déjà livrée
        if ($tontine->delivery_status === 'delivered') {
            return back()->withErrors(['error' => 'Cette tontine a déjà été livrée!']);
        }
        
        $product = $tontine->product;
        
        // Vérifier le stock
        if ($product->stock_quantity < 1) {
            return back()->withErrors(['error' => 'Stock insuffisant pour livrer ce produit!']);
        }
        
        // Décrémenter le stock
        $stockBefore = $product->stock_quantity;
        $stockAfter = $stockBefore - 1;
        
        // Créer le mouvement de stock
        StockMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => 'out',
            'quantity' => 1,
            'stock_before' => $stockBefore,
            'stock_after' => $stockAfter,
            'reference' => 'TONTINE-' . $tontine->id,
            'reason' => 'Livraison tontine #' . $tontine->id . ' - Client: ' . $tontine->client->full_name,
        ]);
        
        // Mettre à jour le stock
        $product->update(['stock_quantity' => $stockAfter]);
        
        // Marquer la tontine comme livrée
        $tontine->update([
            'delivery_status' => 'delivered',
            'delivered_at' => now(),
            'delivered_by' => auth()->id(),
        ]);
        
        return back()->with('success', 'Produit livré! Stock mis à jour.');
    }
}
