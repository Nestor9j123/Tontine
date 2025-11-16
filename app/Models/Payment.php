<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference',
        'tontine_id',
        'client_id',
        'collected_by',
        'amount',
        'daily_amount',
        'days_count',
        'is_multiple_payment',
        'payment_date',
        'payment_method',
        'transaction_id',
        'notes',
        'status',
        'validated_by',
        'validated_at',
        'rejection_reason',
        'uuid',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
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
    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeValidated($query)
    {
        return $query->where('status', 'validated');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('collected_by', $agentId);
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('payment_date', today());
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', ' ') . ' FCFA';
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">En attente</span>',
            'validated' => '<span class="badge bg-success">Validé</span>',
            'rejected' => '<span class="badge bg-danger">Rejeté</span>',
            default => '<span class="badge bg-secondary">Inconnu</span>',
        };
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->reference)) {
                $payment->reference = 'PAY-' . strtoupper(uniqid());
            }
            if (empty($payment->uuid)) {
                $payment->uuid = Str::uuid();
            }
        });
    }
}
