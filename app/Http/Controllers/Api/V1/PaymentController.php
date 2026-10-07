<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\MidtransService;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct(
        private readonly MidtransService $midtrans,
        private readonly StockService $stock,
    ) {}

    /**
     * POST /api/v1/payments/webhook
     * Endpoint webhook Midtrans — dipanggil server-to-server oleh Midtrans.
     * TIDAK memerlukan CSRF middleware (harus diexclude di VerifyCsrfToken atau via route api).
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Midtrans Webhook Received', ['payload' => $payload]);

        try {
            $result = $this->midtrans->handleWebhook($payload);
        } catch (\Throwable $e) {
            Log::error('Midtrans webhook ditolak', [
                'message' => $e->getMessage(),
                'payload' => $payload,
            ]);
            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        // Cari transaksi berdasarkan order_code
        // Strip suffix -R{N} dari order_id Midtrans (digunakan untuk retry pembayaran)
        $orderCode = preg_replace('/-R\d+$/', '', $result['order_code']);
        $transaction = Transaction::where('order_code', $orderCode)->first();

        if (! $transaction) {
            Log::warning('Midtrans webhook: order tidak ditemukan', ['order_code' => $result['order_code']]);
            return response()->json(['message' => 'Order tidak ditemukan.'], 404);
        }

        // Retry order ID harus cocok dengan generasi retry terakhir. Abaikan callback
        // dari percobaan lama agar status terbaru tidak tertimpa notifikasi terlambat.
        $expectedOrderId = $transaction->midtrans_retry_count > 0
            ? $transaction->order_code . '-R' . $transaction->midtrans_retry_count
            : $transaction->order_code;
        if (! hash_equals($expectedOrderId, $result['order_code'])) {
            Log::warning('Midtrans webhook dari percobaan pembayaran lama diabaikan', [
                'received_order_id' => $result['order_code'],
                'expected_order_id' => $expectedOrderId,
            ]);
            return response()->json(['message' => 'Webhook dari percobaan lama diabaikan.']);
        }

        DB::transaction(function () use ($transaction, $result) {
            $locked = Transaction::whereKey($transaction->id)->lockForUpdate()->first();
            if (! $locked) {
                return;
            }

            // Status terminal tidak boleh diturunkan oleh notifikasi duplikat/terlambat.
            $current = $locked->payment_status;
            $incoming = $result['payment_status'];
            if ($current === 'paid' && $incoming !== 'paid') {
                return;
            }
            if (in_array($current, ['cancelled', 'expired', 'failed'], true) && $incoming === 'pending') {
                return;
            }

            $updateData = [
                'payment_status' => $incoming,
                'midtrans_transaction_id' => $result['midtrans_id'],
                'payment_type' => $result['payment_type'],
            ];
            if ($incoming === 'paid') {
                $updateData['paid_at'] = $locked->paid_at ?? now('Asia/Makassar');
                $updateData['order_status'] = 'processing';
            }
            $locked->update($updateData);

            if ($incoming === 'paid') {
                $this->stock->deductForTransaction($locked);
            }
        });

        Log::info('Transaksi diupdate', [
            'order_code'     => $result['order_code'],
            'payment_status' => $result['payment_status'],
        ]);

        return response()->json(['message' => 'Webhook processed.']);
    }
}
