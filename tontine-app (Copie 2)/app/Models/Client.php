<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'first_name',
        'last_name',
        'phone',
        'phone_secondary',
        'email',
        'address',
        'city',
        'id_card_number',
        'photo',
        'agent_id',
        'is_active',
        'has_physical_notebook',
        'notebook_amount_paid',
        'notebook_fully_paid',
        'uuid',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_physical_notebook' => 'boolean',
        'notebook_amount_paid' => 'decimal:2',
        'notebook_fully_paid' => 'boolean',
    ];

    // Relations
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function tontines()
    {
        return $this->hasMany(Tontine::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    
    public function notebookPayments()
    {
        return $this->hasMany(NotebookPayment::class);
    }

    // Accessors
    public function getNotebookRemainingAttribute()
    {
        return 300 - $this->notebook_amount_paid;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Boot method pour générer le code, l'UUID et gérer la suppression en cascade
    protected static function boot()
    {
        parent::boot();

        // Génération automatique du code et UUID lors de la création
        static::creating(function ($client) {
            if (empty($client->code)) {
                $client->code = 'CLI-' . strtoupper(uniqid());
            }
            if (empty($client->uuid)) {
                $client->uuid = Str::uuid();
            }
        });

        // Gestion de la suppression en cascade
        static::deleting(function ($client) {
            // Soft delete des tontines liées
            $client->tontines()->delete();
            
            // Soft delete des paiements liés
            $client->payments()->delete();
            
            // Soft delete des paiements carnet liés
            $client->notebookPayments()->delete();
            
            // Log de l'activité
            if (function_exists('activity') && auth()->check()) {
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($client)
                    ->withProperties([
                        'tontines_count' => $client->tontines()->count(),
                        'payments_count' => $client->payments()->count(),
                        'notebook_payments_count' => $client->notebookPayments()->count(),
                    ])
                    ->log('Client supprimé avec toutes ses données liées');
            }
        });
    }
}
