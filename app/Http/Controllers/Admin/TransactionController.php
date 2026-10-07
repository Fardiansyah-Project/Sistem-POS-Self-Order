<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Http\Requests\TransactionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * GET /cms/admin/api/transactions
     * List transaksi dengan pencarian & filter
     */
    public function index(Request $request): JsonResponse
    {
        $query = Transaction::query();

        // Pencarian (Kode, Nama, atau Meja)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('table_number', 'like', "%{$search}%");
            });
        }

        // Filter Status Pembayaran
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Filter Status Order
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        // Filter Tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Sorting
        $query->orderBy('created_at', 'desc');

        return response()->json(
            $query->paginate(20)
        );
    }

    /**
     * GET /cms/admin/api/transactions/{transaction}
     * Detail transaksi
     */
    public function show(Transaction $transaction): JsonResponse
    {
        $transaction->load('details');
        return response()->json([
            'data' => $transaction
        ]);
    }

    /**
     * PATCH /cms/admin/api/transactions/{transaction}/cancel
     * Batalkan transaksi & kembalikan stok jika perlu
     */
    public function cancel(Transaction $transaction): JsonResponse
    {
        if ($transaction->order_status === 'completed' || $transaction->order_status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak dapat dibatalkan.'
            ], 422);
        }

        $transaction->update([
            'order_status' => 'cancelled'
        ]);

        // (Opsional) Kembalikan stok bahan baku jika pesanan dibatalkan tapi sudah terpotong
        // Implementasikan logika pengembalian stok melalui StockService jika diperlukan.

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dibatalkan.'
        ]);
    }

    /**
     * PATCH /cms/admin/api/transactions/{transaction}/close
     * Tutup pesanan yang sudah selesai diproses dan dibayar.
     */
    public function close(Transaction $transaction): JsonResponse
    {
        if ($transaction->payment_status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan belum dibayar sehingga tidak dapat ditutup.',
            ], 422);
        }

        if (in_array($transaction->order_status, ['completed', 'cancelled'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini sudah ditutup atau dibatalkan.',
            ], 422);
        }

        $transaction->update(['order_status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil ditutup.',
            'data' => ['id' => $transaction->id, 'order_status' => $transaction->order_status],
        ]);
    }

    /**
     * DELETE /cms/admin/api/transactions/bulk-destroy
     * Hapus banyak transaksi (Bulk Delete)
     */
    public function bulkDestroy(TransactionRequest $request): JsonResponse
    {
        $ids = $request->validated()['ids'];
        Transaction::whereIn('id', $ids)->delete();

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' transaksi berhasil dihapus.'
        ]);
    }
}
