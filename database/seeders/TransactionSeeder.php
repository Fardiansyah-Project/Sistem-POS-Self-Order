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
        $datasetPath = database_path('coffee_shop_transactions.json');
        $dataset = json_decode(
            file_get_contents($datasetPath),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (! is_array($dataset) || $dataset === []) {
            throw new RuntimeException('Dataset transaksi kosong atau formatnya tidak valid.');
        }

        $menuProducts = [
            'Latte' => ['catalog_name' => 'Caffe Latte', 'menu_name' => 'Caffee Latte'],
            'Creamy Aren Latte' => ['catalog_name' => 'Koriro Signature Latte', 'menu_name' => 'Koriru'],
            'Matcha Latte' => ['catalog_name' => 'Matcha Latte', 'menu_name' => 'Matcha'],
            'Cappuccino' => ['catalog_name' => 'Cappuccino', 'menu_name' => 'Cappuccino'],
            'Caramel Macchiato' => ['catalog_name' => 'Caramel Salt', 'menu_name' => 'Caramel Salt'],
            'Americano' => ['catalog_name' => 'Americano', 'menu_name' => 'Americano'],
        ];
        $products = Product::available()->get()->keyBy('name');
        $mappedProducts = [];

        foreach ($menuProducts as $datasetName => $menuProduct) {
            $product = $products->get($menuProduct['catalog_name']);

            if (! $product) {
                throw new RuntimeException(
                    sprintf('Produk "%s" belum tersedia. Jalankan ProductSeeder terlebih dahulu.', $menuProduct['catalog_name'])
                );
            }

            $mappedProducts[$datasetName] = [
                'product' => $product,
                'menu_name' => $menuProduct['menu_name'],
            ];
        }

        $datasetKeys = array_keys($dataset[0]);
        foreach (array_keys($menuProducts) as $requiredColumn) {
            if (! in_array($requiredColumn, $datasetKeys, true)) {
                throw new RuntimeException(sprintf('Kolom "%s" tidak ditemukan di dataset transaksi.', $requiredColumn));
            }
        }

        $ignoredColumns = array_values(array_diff($datasetKeys, ['TransactionID', ...array_keys($menuProducts)]));
        $startDate = Carbon::create(2026, 5, 1)->startOfDay();
        $endDate = Carbon::create(2026, 10, 31)->startOfDay();
        $dateRangeInDays = (int) $startDate->diffInDays($endDate);
        $customers = ['Andi', 'Bunga', 'Citra', 'Dimas', 'Rani'];
        $paymentTypes = ['qris', 'gopay', 'bank_transfer'];
        $seededTransactions = 0;
        $skippedTransactions = 0;

        foreach ($dataset as $index => $row) {
            if (! isset($row['TransactionID'])) {
                throw new RuntimeException(sprintf('TransactionID tidak ditemukan pada baris dataset ke-%d.', $index + 1));
            }

            $transactionItems = [];
            foreach ($mappedProducts as $datasetName => $mappedProduct) {
                $quantity = (int) ($row[$datasetName] ?? 0);
                if ($quantity <= 0) {
                    continue;
                }

                $transactionItems[] = [
                    ...$mappedProduct,
                    'quantity' => $quantity,
                ];
            }

            if ($transactionItems === []) {
                $skippedTransactions++;
                continue;
            }

            $transactionId = (int) $row['TransactionID'];
            $dayOffset = count($dataset) > 1
                ? intdiv($index * $dateRangeInDays, count($dataset) - 1)
                : 0;
            $date = $startDate->copy()->addDays($dayOffset)->setTime(
                8 + ($transactionId % 12),
                ($transactionId * 17) % 60
            );
            $orderCode = sprintf('KRC-DATA-%04d', $transactionId);
            $subtotal = collect($transactionItems)->sum(
                fn (array $item) => (float) $item['product']->price * $item['quantity']
            );
            $paidAt = $date->copy()->addMinutes(10 + ($transactionId % 20));

            $transaction = Transaction::updateOrCreate(
                ['order_code' => $orderCode],
                [
                    'customer_name' => $customers[$transactionId % count($customers)],
                    'table_number' => (string) (($transactionId % 20) + 1),
                    'subtotal' => $subtotal,
                    'tax_amount' => 0,
                    'total_amount' => $subtotal,
                    'payment_status' => 'paid',
                    'order_status' => 'completed',
                    'payment_type' => $paymentTypes[$transactionId % count($paymentTypes)],
                    'paid_at' => $paidAt,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]
            );

            foreach ($transactionItems as $item) {
                $product = $item['product'];
                $itemSubtotal = (float) $product->price * $item['quantity'];

                $transaction->details()->updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'product_name' => $item['menu_name'],
                        'unit_price' => $product->price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $itemSubtotal,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]
                );
            }

            $seededTransactions++;
        }

        $this->command?->info(sprintf(
            '%d transaksi berhasil di-seed; %d transaksi tanpa produk menu dilewati. Kolom yang tidak tersedia di menu: %s.',
            $seededTransactions,
            $skippedTransactions,
            implode(', ', $ignoredColumns)
        ));
    }
}