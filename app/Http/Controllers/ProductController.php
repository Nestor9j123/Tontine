<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('photos');
        
        // Filtrage par nom
        if ($request->has("search") && !empty($request->search)) {
            $query->where("name", "like", "%{$request->search}%")
                  ->orWhere("description", "like", "%{$request->search}%");
        }
        
        // Filtrage par type
        if ($request->has("type") && !empty($request->type)) {
            $query->where("type", $request->type);
        }
        
        // Filtrage par statut
        if ($request->has("status")) {
            $query->where("is_active", $request->status === "active");
        }
        
        // Filtrage par prix
        if ($request->has("min_price") && is_numeric($request->min_price)) {
            $query->where("price", ">=", $request->min_price);
        }
        
        if ($request->has("max_price") && is_numeric($request->max_price)) {
            $query->where("price", "<=", $request->max_price);
        }
        
        // Tri
        $sortField = $request->get("sort", "created_at");
        $sortDirection = $request->get("direction", "desc");
        $query->orderBy($sortField, $sortDirection);
        
        $perPage = $request->get("per_page", 15);
        $products = $query->paginate($perPage)->appends($request->except("page"));
        
        return view("products.index", compact("products"));
    }

    public function lowStock(Request $request)
    {
        $query = Product::with('photos');
        
        // Filtrer seulement les produits en stock faible ou rupture
        $query->where('is_active', true)
              ->where(function($q) {
                  $q->where(function($subQ) {
                      // Stock faible (1-10)
                      $subQ->where('stock_quantity', '<=', 10)
                           ->where('stock_quantity', '>', 0);
                  })->orWhere('stock_quantity', 0); // Rupture
              });

        // Trier par stock (rupture en premier, puis stock faible)
        $query->orderByRaw('CASE WHEN stock_quantity = 0 THEN 1 ELSE 2 END')
              ->orderBy('stock_quantity', 'asc')
              ->orderBy('name', 'asc');
        
        $products = $query->paginate(15);
        
        // Ajouter un flag pour indiquer qu'on affiche les stocks faibles
        $showingLowStock = true;
        
        return view("products.index", compact("products", "showingLowStock"));
    }

    public function show(Product $product)
    {
        // Récupérer le produit avec ses photos
        $product->load('photos');
        
        // Récupérer les produits similaires (même type, excluant le produit actuel)
        $similarProducts = Product::where('type', $product->type)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with('photos')
            ->limit(4)
            ->get();
        
        return view('products.show', compact('product', 'similarProducts'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'required|numeric|min:1',
            'purchase_price' => 'nullable|numeric|min:0',
            'duration_value' => 'required|integer|min:1',
            'duration_unit' => 'required|in:days,weeks,months,years',
            'type' => 'required|in:daily,weekly,monthly,yearly',
            'stock_quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Calculer duration_months pour compatibilité
        $validated['duration_months'] = $this->convertToMonths($validated['duration_value'], $validated['duration_unit']);

        // Convertir le statut en booléen
        $validated['is_active'] = $validated['status'] === 'active';
        unset($validated['status']); // Supprimer le champ status car on utilise is_active
        
        // Calculer la marge bénéficiaire si prix d'achat fourni
        if (!empty($validated['purchase_price']) && $validated['purchase_price'] > 0) {
            $validated['profit_margin'] = (($validated['price'] - $validated['purchase_price']) / $validated['purchase_price']) * 100;
        }
        
        $product = Product::create($validated);
        
        // Gérer l'upload des photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $photoPath = $photo->store('products', 'public');
                
                \App\Models\ProductPhoto::create([
                    'product_id' => $product->id,
                    'path' => $photoPath,
                    'is_primary' => $index === 0, // La première photo est la principale
                    'order' => $index,
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Produit créé !');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price' => 'required|numeric|min:1',
            'purchase_price' => 'nullable|numeric|min:0',
            'duration_value' => 'required|integer|min:1',
            'duration_unit' => 'required|in:days,weeks,months,years',
            'type' => 'required|in:daily,weekly,monthly,yearly',
            'stock_quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Calculer la marge bénéficiaire si prix d'achat fourni
        if (!empty($validated['purchase_price']) && $validated['purchase_price'] > 0) {
            $validated['profit_margin'] = (($validated['price'] - $validated['purchase_price']) / $validated['purchase_price']) * 100;
        }
        
        // Calculer duration_months pour compatibilité
        $validated['duration_months'] = $this->convertToMonths($validated['duration_value'], $validated['duration_unit']);

        // Convertir le statut en booléen
        $validated['is_active'] = $validated['status'] === 'active';
        unset($validated['status']); // Supprimer le champ status car on utilise is_active
        
        $product->update($validated);
        
        // Gérer l'upload des nouvelles photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $photoPath = $photo->store('products', 'public');
                
                // Si c'est la première photo et qu'il n'y a pas encore de photo principale, la marquer comme principale
                $isFirst = $index === 0;
                $hasPrimary = $product->photos()->where('is_primary', true)->exists();
                
                \App\Models\ProductPhoto::create([
                    'product_id' => $product->id,
                    'path' => $photoPath,
                    'is_primary' => $isFirst && !$hasPrimary,
                    'order' => $product->photos()->count() + $index,
                ]);
            }
        }

        return redirect()->route('products.index')->with('success', 'Produit mis à jour !');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produit supprimé !');
    }
    
    /**
     * Convertir la durée en mois pour compatibilité
     */
    private function convertToMonths($value, $unit)
    {
        switch ($unit) {
            case 'days':
                return ceil($value / 30); // Approximation: 30 jours = 1 mois
            case 'weeks':
                return ceil($value / 4); // Approximation: 4 semaines = 1 mois
            case 'years':
                return $value * 12; // 1 an = 12 mois
            case 'months':
            default:
                return $value;
        }
    }
}
