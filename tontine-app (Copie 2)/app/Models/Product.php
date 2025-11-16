<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected static function boot()
    {
        parent::boot();
        
        // Générer automatiquement le code et l'UUID lors de la création
        static::creating(function ($product) {
            if (empty($product->code)) {
                $lastProduct = static::orderBy('id', 'desc')->first();
                $nextId = $lastProduct ? $lastProduct->id + 1 : 1;
                $product->code = 'PROD-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
            if (empty($product->uuid)) {
                $product->uuid = Str::uuid();
            }
        });
    }

    protected $fillable = [
        'code',
        'name',
        'description',
        'photo',
        'price',
        'stock_quantity',
        'min_stock_alert',
        'duration_months',
        'duration_value',
        'duration_unit',
        'type',
        'is_active',
        'uuid',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Relations
    public function tontines()
    {
        return $this->hasMany(Tontine::class);
    }
    
    public function photos()
    {
        return $this->hasMany(ProductPhoto::class)->orderBy('order');
    }
    
    public function primaryPhoto()
    {
        return $this->hasOne(ProductPhoto::class)->where('is_primary', true);
    }
    
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDaily($query)
    {
        return $query->where('type', 'daily');
    }

    public function scopeWeekly($query)
    {
        return $query->where('type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('type', 'monthly');
    }
    
    public function scopeYearly($query)
    {
        return $query->where('type', 'yearly');
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', ' ') . ' FCFA';
    }
    
    public function getFormattedDurationAttribute()
    {
        $value = $this->duration_value ?? 1;
        $unit = $this->duration_unit ?? 'months';
        
        // Conversion intelligente
        if ($unit === 'months' && $value >= 12 && $value % 12 === 0) {
            // 12 mois = 1 an, 24 mois = 2 ans, etc.
            $years = $value / 12;
            return $years . ' ' . ($years > 1 ? 'ans' : 'an');
        }
        
        if ($unit === 'weeks' && $value >= 52 && $value % 52 === 0) {
            // 52 semaines = 1 an
            $years = $value / 52;
            return $years . ' ' . ($years > 1 ? 'ans' : 'an');
        }
        
        if ($unit === 'days' && $value >= 365 && $value % 365 === 0) {
            // 365 jours = 1 an
            $years = $value / 365;
            return $years . ' ' . ($years > 1 ? 'ans' : 'an');
        }
        
        $unitText = [
            'days' => $value > 1 ? 'jours' : 'jour',
            'weeks' => $value > 1 ? 'semaines' : 'semaine',
            'months' => $value > 1 ? 'mois' : 'mois',
            'years' => $value > 1 ? 'ans' : 'an',
        ];
        
        return $value . ' ' . ($unitText[$unit] ?? 'mois');
    }
}
