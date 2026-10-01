<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Kurangi stok berdasarkan detail order dan resep produk.
     * Operasi dibuat idempotent agar webhook dan kasir tidak mengurangi stok dua kali.
     */
    public function deductForTransaction(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction): void {
            $lockedTransaction = Transaction::whereKey($transaction->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedTransaction->stock_deducted_at) {
                return;
            }

            $details = $lockedTransaction->details()
                ->with('product.recipes')
                ->get();

            $requiredStock = [];
            foreach ($details as $detail) {
                foreach ($detail->product->recipes as $recipe) {
                    $requiredStock[$recipe->ingredient_id] =
                        ($requiredStock[$recipe->ingredient_id] ?? 0)
                        + ((float) $detail->quantity * (float) $recipe->quantity_needed);
                }
            }

            foreach ($requiredStock as $ingredientId => $quantity) {
                $ingredient = Ingredient::whereKey($ingredientId)->lockForUpdate()->firstOrFail();
                $ingredient->decrement('stock_quantity', $quantity);
            }

            $lockedTransaction->update(['stock_deducted_at' => now('Asia/Makassar')]);
        });
    }
}