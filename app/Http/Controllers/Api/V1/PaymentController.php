<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\MidtransService;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

        // Update status pembayaran
        $updateData = [
            'payment_status'           => $result['payment_status'],
            'midtrans_transaction_id'  => $result['midtrans_id'],
            'payment_type'             => $result['payment_type'],
        ];

        if ($result['payment_status'] === 'paid') {
            $updateData['paid_at']      = now('Asia/Makassar');
            $updateData['order_status'] = 'processing';
        }

        $transaction->update($updateData);

        if ($result['payment_status'] === 'paid') {
            $this->stock->deductForTransaction($transaction);
        }

        Log::info('Transaksi diupdate', [
            'order_code'     => $result['order_code'],
            'payment_status' => $result['payment_status'],
        ]);

        return response()->json(['message' => 'Webhook processed.']);
    }
}
