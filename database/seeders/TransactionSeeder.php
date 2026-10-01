<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use RuntimeException;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::available()->get();

        if ($products->isEmpty()) {
            throw new RuntimeException('Produk harus tersedia sebelum transaksi di-seed.');
        }

        $customers = ['Andi', 'Bunga', 'Citra'];
        $paymentTypes = ['qris', 'gopay', 'bank_transfer'];
        $productIndex = 0;

        for ($month = 1; $month <= 8; $month++) {
            for ($transactionNumber = 1; $transactionNumber <= 3; $transactionNumber++) {
                $date = Carbon::create(2026, $month, 5 + (($transactionNumber - 1) * 10));
                $orderCode = sprintf('KRC-%s-%03d', $date->format('Ymd'), $transactionNumber);
                $product = $products[$productIndex % $products->count()];
                $quantity = ($transactionNumber % 2) + 1;
                $subtotal = (float) $product->price * $quantity;
                $paidAt = $date->copy()->addMinutes(10 + $transactionNumber);

                $transaction = Transaction::updateOrCreate(
                    ['order_code' => $orderCode],
                    [
                        'customer_name' => $customers[($transactionNumber - 1) % count($customers)],
                        'table_number' => (string) $transactionNumber,
                        'subtotal' => $subtotal,
                        'tax_amount' => 0,
                        'total_amount' => $subtotal,
                        'payment_status' => 'paid',
                        'order_status' => 'completed',
                        'payment_type' => $paymentTypes[($transactionNumber - 1) % count($paymentTypes)],
                        'paid_at' => $paidAt,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]
                );

                $transaction->details()->updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'product_name' => $product->name,
                        'unit_price' => $product->price,
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]
                );

                $productIndex++;
            }
        }
    }
}