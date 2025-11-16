<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TontineNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'tontine_id',
        'client_id',
        'agent_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
        'uuid',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($notification) {
            if (empty($notification->uuid)) {
                $notification->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relations
    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Créer une notification de fin de paiement
     */
    public static function createPaymentCompletedNotification(Tontine $tontine)
    {
        return self::create([
            'tontine_id' => $tontine->id,
            'client_id' => $tontine->client_id,
            'agent_id' => $tontine->agent_id,
            'type' => 'payment_completed',
            'title' => 'Paiements terminés',
            'message' => "Le client {$tontine->client->full_name} a terminé ses paiements pour {$tontine->product->name}. Produit prêt à être livré.",
        ]);
    }

    /**
     * Créer une notification de stock faible
     */
    public static function createLowStockNotification(Product $product, $agentId = null)
    {
        return self::create([
            'tontine_id' => null,
            'client_id' => null,
            'agent_id' => $agentId,
            'type' => 'low_stock',
            'title' => 'Stock faible',
            'message' => "Le produit {$product->name} a un stock faible ({$product->stock_quantity} restant).",
        ]);
    }
}
