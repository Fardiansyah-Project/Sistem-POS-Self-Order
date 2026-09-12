<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'is_available',
        'sort_order',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'is_available' => 'boolean',
    ];

    // Relasi ke kategori
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke detail transaksi
    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // Relasi ke bahan baku melalui resep (Many-to-Many)
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'recipes')
                    ->withPivot('quantity_needed')
                    ->withTimestamps();
    }

    // Relasi langsung ke resep
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    // Scope: hanya produk yang tersedia
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->orderBy('sort_order');
    }

    // Accessor: URL gambar produk
    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/product-placeholder.png');
    }
}
