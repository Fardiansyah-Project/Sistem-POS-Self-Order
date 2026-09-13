<?php

namespace App\Services;

use App\Models\Transaction;
use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Midtrans\Transaction as MidtransTransaction;
use Midtrans\Notification;

/**
 * MidtransService — wrapper untuk Midtrans PHP SDK.
 * Menangani pembuatan Snap token dan verifikasi webhook.
 */
class MidtransService
{
    public function __construct()
    {
        // Konfigurasi Midtrans dari .env
        MidtransConfig::$serverKey    = config('services.midtrans.server_key');
        MidtransConfig::$clientKey    = config('services.midtrans.client_key');
        MidtransConfig::$isProduction = config('services.midtrans.is_production', false);
        MidtransConfig::$isSanitized  = true;
        MidtransConfig::$is3ds        = true;
    }

    /**
     * Buat Snap Token untuk pembayaran baru.
     *
     * @param  Transaction $transaction  Data transaksi yang sudah tersimpan di DB
     * @return string                   Snap token untuk digunakan di frontend
     * @throws \Exception               Jika Midtrans API error
     */
    public function createSnapToken(Transaction $transaction): string
    {
        $params = [
            'transaction_details' => [
                'order_id'     => $transaction->order_code,
                'gross_amount' => (int) $transaction->total_amount,
            ],
            'customer_details' => [
                'first_name' => $transaction->customer_name,
            ],
            'item_details' => $transaction->details->map(function ($detail) {
                return [
                    'id'       => (string) $detail->product_id,
                    'price'    => (int) $detail->unit_price,
                    'quantity' => $detail->quantity,
                    'name'     => $detail->product_name,
                ];
            })->toArray(),
            'callbacks' => [
                'finish' => config('app.url') . '/order/' . $transaction->order_code,
            ],
        ];

        // Tambahkan tax jika ada
        if ($transaction->tax_amount > 0) {
            $params['item_details'][] = [
                'id'       => 'TAX',
                'price'    => (int) $transaction->tax_amount,
                'quantity' => 1,
                'name'     => 'Pajak (PPN)',
            ];
        }

        $snapToken = Snap::getSnapToken($params);

        // Simpan snap token ke database
        $transaction->update(['snap_token' => $snapToken]);

        return $snapToken;
    }

    /**
     * Sinkronkan status transaksi dari Midtrans jika webhook terlambat atau gagal.
     */
    public function syncTransactionStatus(Transaction $transaction): ?string
    {
        try {
            $midtransStatus = MidtransTransaction::status($transaction->order_code);
            $transactionStatus = $midtransStatus->transaction_status ?? null;
            $fraudStatus = $midtransStatus->fraud_status ?? null;

            if (! $transactionStatus) {
                return null;
            }

            $paymentStatus = $this->mapPaymentStatus($transactionStatus, $fraudStatus);
            $updateData = [
                'payment_status' => $paymentStatus,
                'payment_type' => $midtransStatus->payment_type ?? $transaction->payment_type,
            ];

            if ($paymentStatus === 'paid') {
                $updateData['paid_at'] = $transaction->paid_at ?? now();
                $updateData['order_status'] = 'processing';
            }

            $transaction->update($updateData);

            return $paymentStatus;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    /**
     * Proses notifikasi webhook dari Midtrans dan verifikasi signature.
     *
     * @param  array  $payload  Data payload dari webhook Midtrans
     * @return array            ['status' => string, 'transaction_id' => string, 'payment_type' => string]
     * @throws \Exception       Jika signature key tidak valid
     */
    public function handleWebhook(array $payload): array
    {
        foreach (['order_id', 'status_code', 'gross_amount', 'signature_key', 'transaction_status', 'transaction_id'] as $field) {
            if (! isset($payload[$field])) {
                throw new \InvalidArgumentException("Field webhook Midtrans tidak lengkap: {$field}");
            }
        }

        // Verifikasi signature key
        $serverKey        = config('services.midtrans.server_key');
        $orderId          = $payload['order_id'];
        $statusCode       = $payload['status_code'];
        $grossAmount      = $payload['gross_amount'];
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if (! hash_equals($expectedSignature, $payload['signature_key'])) {
            throw new \Exception('Signature key Midtrans tidak valid.');
        }

        // Map transaction_status Midtrans ke status internal
        $transactionStatus = $payload['transaction_status'];
        $fraudStatus       = $payload['fraud_status'] ?? null;

        $paymentStatus = $this->mapPaymentStatus($transactionStatus, $fraudStatus);

        return [
            'order_code'     => $orderId,
            'payment_status' => $paymentStatus,
            'payment_type'   => $payload['payment_type'] ?? null,
            'midtrans_id'    => $payload['transaction_id'],
        ];
    }

    private function mapPaymentStatus(string $transactionStatus, ?string $fraudStatus): string
    {
        return match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'paid',
            $transactionStatus === 'settlement'                            => 'paid',
            $transactionStatus === 'cancel'                                => 'cancelled',
            $transactionStatus === 'deny'                                  => 'failed',
            $transactionStatus === 'expire'                                => 'expired',
            default                                                        => 'pending',
        };
    }
}
