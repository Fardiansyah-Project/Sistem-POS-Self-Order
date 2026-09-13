<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private readonly MidtransService $midtrans) {}

    /**
     * POST /api/v1/orders
     * Buat pesanan baru dari self-order kiosk, lalu minta Snap Token ke Midtrans.
     *
     * Request body:
     * {
     *   "customer_name": "Budi",
     *   "table_number": "A3",   // optional
     *   "notes": "...",          // optional
     *   "items": [
     *     { "product_id": 1, "quantity": 2, "notes": "less sugar" },
     *     ...
     *   ]
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name'      => ['required', 'string', 'max:100'],
            'table_number'       => ['nullable', 'string', 'max:20'],
            'notes'              => ['nullable', 'string', 'max:500'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1', 'max:20'],
            'items.*.notes'      => ['nullable', 'string', 'max:200'],
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $details  = [];

            // Validasi produk dan hitung subtotal
            foreach ($validated['items'] as $item) {
                $product = Product::where('id', $item['product_id'])
                                  ->where('is_available', true)
                                  ->firstOrFail();

                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal    += $itemSubtotal;

                $details[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'unit_price'   => $product->price,
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $itemSubtotal,
                    'notes'        => $item['notes'] ?? null,
                ];
            }

            // Hitung pajak (PPN 11% — bisa dikonfigurasi)
            $taxRate   = 0; // set 0.11 jika PPN diaktifkan
            $taxAmount = round($subtotal * $taxRate, 2);
            $total     = $subtotal + $taxAmount;

            // Buat transaksi
            $transaction = Transaction::create([
                'order_code'     => Transaction::generateOrderCode(),
                'customer_name'  => $validated['customer_name'],
                'table_number'   => $validated['table_number'] ?? null,
                'notes'          => $validated['notes'] ?? null,
                'subtotal'       => $subtotal,
                'tax_amount'     => $taxAmount,
                'total_amount'   => $total,
                'payment_status' => 'pending',
                'order_status'   => 'waiting',
            ]);

            // Simpan detail item
            foreach ($details as $detail) {
                $transaction->details()->create($detail);
            }

            // Load details untuk Midtrans
            $transaction->load('details');

            // Dapatkan Snap Token dari Midtrans
            $snapToken = $this->midtrans->createSnapToken($transaction);

            return response()->json([
                'message'    => 'Pesanan berhasil dibuat.',
                'order_code' => $transaction->order_code,
                'snap_token' => $snapToken,
                'total'      => (float) $transaction->total_amount,
            ], 201);
        });
    }

    /**
     * POST /api/v1/orders/{order_code}/payment-token
     * Buat token Snap baru untuk pesanan yang belum dibayar.
     */
    public function paymentToken(string $orderCode): JsonResponse
    {
        $transaction = Transaction::where('order_code', $orderCode)
            ->with('details')
            ->firstOrFail();

        if ($transaction->payment_status !== 'pending') {
            return response()->json([
                'message' => 'Pesanan ini tidak dapat dibayar ulang.',
            ], 422);
        }

        return response()->json([
            'order_code' => $transaction->order_code,
            'snap_token' => $this->midtrans->createSnapToken($transaction),
        ]);
    }
}
