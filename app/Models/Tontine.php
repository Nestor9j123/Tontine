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
        if ($this->total_payments == 0) return 0;
        return round(($this->completed_payments / $this->total_payments) * 100, 2);
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
