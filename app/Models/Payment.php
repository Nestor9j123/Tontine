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
        'expected_amount',
        'missing_amount',
        'is_partial_payment',
        'has_missing_payment',
        'missing_paid_amount',
        'missing_completed_at',
        'completed_by',
        'missing_notes',
        'payment_status',
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
        'expected_amount' => 'decimal:2',
        'missing_amount' => 'decimal:2',
        'missing_paid_amount' => 'decimal:2',
        'is_partial_payment' => 'boolean',
        'has_missing_payment' => 'boolean',
        'payment_date' => 'date',
        'validated_at' => 'datetime',
        'missing_completed_at' => 'datetime',
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

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function paymentHistory()
    {
        return $this->hasMany(PaymentHistory::class)->orderBy('action_date');
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

    public function scopePartialPayments($query)
    {
        return $query->where('is_partial_payment', true);
    }

    public function scopeWithMissingAmount($query)
    {
        return $query->where('has_missing_payment', true);
    }

    public function scopeCompletedPayments($query)
    {
        return $query->where('payment_status', 'complete');
    }

    public function scopeMissingPaid($query)
    {
        return $query->where('payment_status', 'missing_paid');
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

    public function getPaymentStatusBadgeAttribute()
    {
        return match($this->payment_status) {
            'complete' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Complet</span>',
            'partial' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Partiel</span>',
            'missing_paid' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Manquant payé</span>',
            default => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inconnu</span>',
        };
    }

    public function getFormattedExpectedAmountAttribute()
    {
        return $this->expected_amount ? number_format($this->expected_amount, 0, ',', ' ') . ' FCFA' : null;
    }

    public function getFormattedMissingAmountAttribute()
    {
        return number_format($this->missing_amount, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedMissingPaidAmountAttribute()  
    {
        return number_format($this->missing_paid_amount, 0, ',', ' ') . ' FCFA';
    }

    public function getRemainingMissingAmountAttribute()
    {
        return $this->missing_amount - $this->missing_paid_amount;
    }

    public function getFormattedRemainingMissingAmountAttribute()
    {
        return number_format($this->remaining_missing_amount, 0, ',', ' ') . ' FCFA';
    }

    public function getIsCompletedAttribute()
    {
        return $this->payment_status === 'complete' || 
               ($this->has_missing_payment && $this->remaining_missing_amount <= 0);
    }

    // Methods for partial payments management
    public function addMissingPayment($amount, $recordedBy, $notes = null)
    {
        $remainingAmount = $this->remaining_missing_amount - $amount;
        
        // Update payment
        $this->missing_paid_amount += $amount;
        
        if ($remainingAmount <= 0) {
            $this->payment_status = 'missing_paid';
            $this->missing_completed_at = now();
            $this->completed_by = $recordedBy;
        }
        
        $this->save();

        // Add to history
        PaymentHistory::create([
            'payment_id' => $this->id,
            'client_id' => $this->client_id,
            'agent_id' => $this->collected_by,
            'recorded_by' => $recordedBy,
            'action_type' => $remainingAmount <= 0 ? 'completion' : 'missing_payment',
            'amount' => $amount,
            'expected_amount' => $this->expected_amount,
            'remaining_amount' => max(0, $remainingAmount),
            'notes' => $notes,
            'action_date' => now(),
        ]);

        return $this;
    }

    public function createInitialPaymentHistory($recordedBy)
    {
        PaymentHistory::create([
            'payment_id' => $this->id,
            'client_id' => $this->client_id,
            'agent_id' => $this->collected_by,
            'recorded_by' => $recordedBy,
            'action_type' => 'initial_payment',
            'amount' => $this->amount,
            'expected_amount' => $this->expected_amount,
            'remaining_amount' => $this->missing_amount,
            'notes' => $this->notes,
            'action_date' => $this->payment_date,
        ]);
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
