<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'stock_quantity',
        'minimum_stock',
        'cost_per_unit',
    ];

    protected $casts = [
        'stock_quantity' => 'decimal:3',
        'minimum_stock'  => 'decimal:3',
        'cost_per_unit'  => 'decimal:2',
    ];

    // Relasi ke resep
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    // Relasi ke produk melalui resep (Many-to-Many)
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'recipes')
                    ->withPivot('quantity_needed')
                    ->withTimestamps();
    }

    // Relasi ke histori peramalan
    public function forecasts(): HasMany
    {
        return $this->hasMany(RawMaterialForecast::class);
    }

    // Accessor: apakah stok di bawah minimum (kritis)
    public function getIsStockCriticalAttribute(): bool
    {
        return $this->stock_quantity <= $this->minimum_stock;
    }
}
