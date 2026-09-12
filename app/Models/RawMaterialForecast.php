<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RawMaterialForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'period_date',
        'actual_usage',
        'forecasted_amount',
        'historical_data',
        'wma_weights',
        'mean_absolute_error',
    ];

    protected $casts = [
        'period_date'          => 'date',
        'actual_usage'         => 'decimal:3',
        'forecasted_amount'    => 'decimal:3',
        'historical_data'      => 'array',  // JSON → PHP array otomatis
        'wma_weights'          => 'array',  // JSON → PHP array otomatis
        'mean_absolute_error'  => 'decimal:4',
    ];

    // Relasi ke bahan baku
    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }
}
