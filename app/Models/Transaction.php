<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_name',
        'table_number',
        'notes',
        'subtotal',
        'tax_amount',
        'total_amount',
        'payment_status',
        'order_status',
        'midtrans_transaction_id',
        'snap_token',
        'payment_type',
        'paid_at',
        'stock_deducted_at',
        'midtrans_retry_count',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'tax_amount'   => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at'      => 'datetime',
        'stock_deducted_at' => 'datetime',
    ];

    // Relasi ke detail item pesanan
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    // Generate order code unik: KRC-YYYYMMDD-XXXX
    public static function generateOrderCode(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(\Illuminate\Support\Str::random(4));
        return sprintf('KRC-%s-%s', $date, $random);
    }

    // Scope: pesanan hari ini
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Scope: pesanan yang sudah dibayar
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }
}
