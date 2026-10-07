<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\StockService;
use App\Http\Requests\KitchenRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function __construct(private readonly StockService $stock) {}

    /**
     * GET /cms/admin/api/kitchen
     * Ambil daftar pesanan aktif untuk monitor dapur.
     */
    public function index(): JsonResponse
    {
        // Hanya ambil transaksi yang sudah dibayar, dan statusnya belum completed/cancelled
        $activeOrders = Transaction::with('details')
            ->where('payment_status', 'paid')
            ->whereIn('order_status', ['waiting', 'processing', 'ready'])
            ->orderBy('paid_at', 'asc') // Yang bayar duluan, diproses duluan
            ->get()
            ->map(function ($order) {
                return [
                    'id'            => $order->id,
                    'order_code'    => $order->order_code,
                    'customer_name' => $order->customer_name,
                    'table_number'  => $order->table_number,
                    'order_status'  => $order->order_status,
                    'notes'         => $order->notes,
                    'created_at'    => $order->created_at->toISOString(),
                    'time_ago'      => $order->created_at->diffForHumans(),
                    'details'       => $order->details->map(fn($d) => [
                        'product_name' => $d->product_name,
                        'quantity'     => $d->quantity,
                        'notes'        => $d->notes,
                    ]),
                ];
            });

        return response()->json([
            'data' => $activeOrders,
        ]);
    }

    /**
     * PATCH /cms/admin/api/kitchen/{transaction}/status
     * Update status pesanan di dapur.
     */
    public function updateStatus(KitchenRequest $request, Transaction $transaction): JsonResponse
    {
        $validated = $request->validated();

        if ($validated['order_status'] === 'processing' && $transaction->order_status !== 'processing') {
            $this->stock->deductForTransaction($transaction);
        }

        $transaction->update([
            'order_status' => $validated['order_status']
        ]);

        $message = match($validated['order_status']) {
            'processing' => 'Pesanan sedang diproses di dapur.',
            'ready'      => 'Pesanan siap untuk diambil pelanggan.',
            'completed'  => 'Pesanan telah selesai/diambil.',
            default      => 'Status pesanan diperbarui.'
        };

        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => [
                'id'           => $transaction->id,
                'order_status' => $transaction->order_status,
            ],
        ]);
    }
}
