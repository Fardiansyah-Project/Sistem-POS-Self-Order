<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'product_name',
        'unit_price',
        'quantity',
        'subtotal',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'subtotal'   => 'decimal:2',
    ];

    // Relasi ke transaksi
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // Relasi ke produk (nullable jika produk dihapus, snapshot tetap ada)
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withDefault([
            'name'  => $this->product_name,
            'price' => $this->unit_price,
        ]);
    }
}
