<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'payment_history';

    protected $fillable = [
        'payment_id',
        'client_id',
        'agent_id',
        'recorded_by',
        'action_type',
        'amount',
        'expected_amount',
        'remaining_amount',
        'notes',
        'metadata',
        'action_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expected_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'metadata' => 'array',
        'action_date' => 'datetime',
    ];

    // Relations
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    // Scopes
    public function scopeForPayment($query, $paymentId)
    {
        return $query->where('payment_id', $paymentId)->orderBy('action_date');
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId)->orderBy('action_date', 'desc');
    }

    public function scopeForAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId)->orderBy('action_date', 'desc');
    }

    // Accessors
    public function getActionTypeLabelAttribute()
    {
        return match($this->action_type) {
            'initial_payment' => 'Paiement initial',
            'missing_payment' => 'Paiement manquant', 
            'completion' => 'Complément de paiement',
            'adjustment' => 'Ajustement',
            default => 'Inconnu',
        };
    }

    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0, ',', ' ') . ' FCFA';
    }

    public function getFormattedRemainingAmountAttribute()
    {
        return number_format($this->remaining_amount, 0, ',', ' ') . ' FCFA';
    }
}
