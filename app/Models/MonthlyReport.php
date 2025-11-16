<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MonthlyReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_period',
        'report_month',
        'report_year',
        'initial_stock',
        'final_stock',
        'products_sold',
        'total_revenue',
        'total_expenses',
        'net_result',
        'payment_stats',
        'agent_performance',
        'generated_by',
        'generated_at',
        'uuid',
    ];

    protected $casts = [
        'initial_stock' => 'array',
        'final_stock' => 'array',
        'products_sold' => 'array',
        'total_revenue' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_result' => 'decimal:2',
        'payment_stats' => 'array',
        'agent_performance' => 'array',
        'generated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($report) {
            if (empty($report->uuid)) {
                $report->uuid = Str::uuid();
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
    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    // Scopes
    public function scopeForMonth($query, $month, $year)
    {
        return $query->where('report_month', $month)
                     ->where('report_year', $year);
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('report_year', $year);
    }
}
