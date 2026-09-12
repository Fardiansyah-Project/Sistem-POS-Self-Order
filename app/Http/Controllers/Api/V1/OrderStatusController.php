<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;

class OrderStatusController extends Controller
{
    /**
     * GET /api/v1/orders/{order_code}/status
     * Cek status pembayaran dan status pesanan (untuk polling dari React).
     */
    public function show(string $orderCode): JsonResponse
    {
        $transaction = Transaction::where('order_code', $orderCode)
            ->with('details:id,transaction_id,product_name,quantity,unit_price,subtotal')
            ->select('id', 'order_code', 'customer_name', 'table_number', 'total_amount', 'payment_status', 'order_status', 'payment_type', 'paid_at', 'created_at')
            ->firstOrFail();

        return response()->json([
            'data' => [
                'order_code'     => $transaction->order_code,
                'customer_name'  => $transaction->customer_name,
                'table_number'   => $transaction->table_number,
                'total_amount'   => (float) $transaction->total_amount,
                'payment_status' => $transaction->payment_status,
                'order_status'   => $transaction->order_status,
                'payment_type'   => $transaction->payment_type,
                'paid_at'        => $transaction->paid_at?->toISOString(),
                'created_at'     => $transaction->created_at->toISOString(),
                'items'          => $transaction->details,
            ],
        ]);
    }
}
