<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TontineNotification extends Model
{
    use HasFactory, SoftDeletes;

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
        'is_delivered',
        'marked_delivered_by',
        'marked_delivered_at',
        'deleted_by',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_delivered' => 'boolean',
        'read_at' => 'datetime',
        'marked_delivered_at' => 'datetime',
        'deleted_at' => 'datetime',
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

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function markedDeliveredBy()
    {
        return $this->belongsTo(User::class, 'marked_delivered_by');
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

    public function markAsDelivered($userId)
    {
        $this->update([
            'is_delivered' => true,
            'marked_delivered_by' => $userId,
            'marked_delivered_at' => now(),
        ]);
    }

    public function canBeDeletedBy($user)
    {
        return $user->hasRole(['super_admin', 'secretary']);
    }

    public function canBeMarkedAsDeliveredBy($user)
    {
        return $user->hasRole(['super_admin', 'secretary', 'agent']);
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
