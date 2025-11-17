<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tontine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'client_id',
        'product_id',
        'agent_id',
        'start_date',
        'end_date',
        'total_amount',
        'paid_amount',
        'remaining_amount',
        'total_payments',
        'completed_payments',
        'status',
        'delivery_status',
        'delivered_at',
        'delivered_by',
        'delivery_notes',
        'notes',
        'validated_by',
        'validated_at',
        'uuid',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'delivered_at' => 'datetime',
        'validated_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relations
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
    
    public function deliverer()
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // Accessors
    public function getProgressPercentageAttribute()
    {
        if ($this->total_amount == 0) return 0;
        
        // Calculer le pourcentage basé sur les montants réels payés (validés + auto-validés)
        // Utiliser le champ paid_amount qui est mis à jour lors de chaque paiement
        $percentage = ($this->paid_amount / $this->total_amount) * 100;
        
        // S'assurer que le pourcentage ne dépasse pas 100%
        return round(min($percentage, 100), 2);
    }

    /**
     * Calculer la progression basée uniquement sur les paiements validés
     */
    public function getValidatedProgressPercentageAttribute()
    {
        if ($this->total_amount == 0) return 0;
        
        $validatedAmount = $this->payments()->where('status', 'validated')->sum('amount');
        $percentage = ($validatedAmount / $this->total_amount) * 100;
        
        return round(min($percentage, 100), 2);
    }

    public function getFormattedTotalAmountAttribute()
    {
        return number_format($this->total_amount, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedPaidAmountAttribute()
    {
        return number_format($this->paid_amount, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedRemainingAmountAttribute()
    {
        return number_format($this->remaining_amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Calculer le montant réellement payé (paiements validés uniquement)
     */
    public function getAmountPaidAttribute()
    {
        return $this->payments()->where('status', 'validated')->sum('amount');
    }

    /**
     * Calculer le montant total des paiements (validés + en attente)
     */
    public function getTotalPaymentsAmountAttribute()
    {
        return $this->payments()->sum('amount');
    }

    /**
     * Marquer la tontine comme livrée et décrémenter le stock du produit
     */
    public function markAsDelivered($userId)
    {
        // Vérifier que la tontine est éligible à la livraison
        if ($this->delivery_status === 'delivered') {
            throw new \Exception('Cette tontine a déjà été livrée.');
        }

        if ($this->status !== 'completed') {
            throw new \Exception('Cette tontine n\'est pas terminée et ne peut pas être livrée.');
        }

        // Transaction pour s'assurer de la cohérence
        \DB::transaction(function () use ($userId) {
            // Mettre à jour le statut de livraison de la tontine
            $this->update([
                'delivery_status' => 'delivered',
                'delivered_at' => now(),
                'delivered_by' => $userId,
            ]);

            // Décrémenter le stock du produit
            if ($this->product && $this->product->stock_quantity > 0) {
                $stockBefore = $this->product->stock_quantity;
                $this->product->decrement('stock_quantity', 1);
                // Refresh pour obtenir la nouvelle valeur
                $this->product->refresh();
                $stockAfter = $this->product->stock_quantity;
                
                // Log de l'activité de stock avec les bons champs
                \App\Models\StockMovement::create([
                    'product_id' => $this->product_id,
                    'user_id' => $userId,
                    'type' => 'out',
                    'quantity' => 1,
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => "Tontine {$this->code}",
                    'reason' => "Livraison tontine - Client: {$this->client->full_name}",
                ]);
            }
        });

        return $this;
    }

    /**
     * Vérifier si la tontine peut être livrée
     */
    public function canBeDelivered()
    {
        return $this->status === 'completed' && 
               $this->delivery_status === 'pending' &&
               $this->product &&
               $this->product->stock_quantity > 0;
    }

    /**
     * Vérifier si la tontine est livrée
     */
    public function isDelivered()
    {
        return $this->delivery_status === 'delivered';
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tontine) {
            if (empty($tontine->code)) {
                $tontine->code = 'TON-' . strtoupper(uniqid());
            }
            if (empty($tontine->uuid)) {
                $tontine->uuid = Str::uuid();
            }
        });
    }
}
