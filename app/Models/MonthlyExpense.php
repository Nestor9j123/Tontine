<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MonthlyExpense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'description',
        'type',
        'amount',
        'user_id',
        'expense_date',
        'expense_month',
        'expense_year',
        'notes',
        'created_by',
        'uuid',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($expense) {
            if (empty($expense->uuid)) {
                $expense->uuid = Str::uuid();
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeForMonth($query, $month, $year)
    {
        return $query->where('expense_month', $month)
                     ->where('expense_year', $year);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByAgent($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getTypeHumanAttribute()
    {
        $types = [
            'electricity' => 'Électricité',
            'rent' => 'Loyer',
            'agent_expense' => 'Dépenses Agent',
            'general' => 'Général'
        ];

        return $types[$this->type] ?? $this->type;
    }
}
