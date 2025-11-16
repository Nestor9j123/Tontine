<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Liste paginée des produits.
     */
    public function index(Request $request)
    {
        $query = Product::query()->withCount('tontines');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $perPage = min((int) $request->get('per_page', 20), 100);
        $products = $query->latest()->paginate($perPage)->appends($request->except('page'));

        return response()->json([
            'data' => $products->getCollection()->map(fn ($p) => $this->transformProduct($p)),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ],
        ]);
    }

    /**
     * Détail d'un produit.
     */
    public function show(Product $product)
    {
        $product->load(['photos', 'primaryPhoto']);

        return response()->json([
            'data' => $this->transformProduct($product, detailed: true),
        ]);
    }

    /**
     * Transformer un produit pour la réponse API.
     */
    private function transformProduct(Product $product, bool $detailed = false): array
    {
        $base = [
            'id' => $product->id,
            'uuid' => $product->uuid,
            'code' => $product->code,
            'name' => $product->name,
            'price' => $product->price,
            'stock_quantity' => $product->stock_quantity,
            'is_active' => (bool) $product->is_active,
            'type' => $product->type,
            'duration_value' => $product->duration_value,
            'duration_unit' => $product->duration_unit,
            'formatted_duration' => $product->formatted_duration,
            'tontines_count' => $product->tontines_count ?? null,
        ];

        if (! $detailed) {
            return $base;
        }

        return array_merge($base, [
            'description' => $product->description,
            'photos' => $product->photos->map(fn ($photo) => [
                'id' => $photo->id,
                'path' => $photo->path,
                'is_primary' => (bool) $photo->is_primary,
            ]),
        ]);
    }
}
