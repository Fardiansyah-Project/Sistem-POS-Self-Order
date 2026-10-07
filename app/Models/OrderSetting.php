<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSetting extends Model
{
    protected $fillable = ['is_open'];

    protected function casts(): array
    {
        return ['is_open' => 'boolean'];
    }

    public static function current(): self
    {
        return static::firstOrCreate([], ['is_open' => true]);
    }
}
