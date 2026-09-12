<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'ingredient_id',
        'quantity_needed',
    ];

    protected $casts = [
        'quantity_needed' => 'decimal:3',
    ];

    // Relasi ke produk
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke bahan baku
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
