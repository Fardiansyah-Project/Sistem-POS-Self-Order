<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(private readonly MidtransService $midtrans) {}

    /**
     * Menampilkan daftar semua transaksi.
     */
    public function index(Request $request)
    {
        $query = Transaction::with('details')
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan status pembayaran jika ada
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Tampilkan 15 data per halaman
        $transactions = $query->paginate(15);

        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Hapus transaksi yang dipilih secara massal (bulk delete).
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:transactions,id'],
        ]);

        $transactions = Transaction::whereIn('id', $validated['ids'])->get();

        foreach ($transactions as $transaction) {
            // Hapus detail item terlebih dahulu, lalu transaksi
            $transaction->details()->delete();
            $transaction->delete();
        }

        return redirect()
            ->route('admin.transactions.index')
            ->with('success', count($validated['ids']) . ' transaksi berhasil dihapus.');
    }

    /**
     * Batalkan pesanan yang masih pending.
     * Juga cancel di Midtrans jika transaksi sudah pernah dibuat.
     */
    public function cancel(Transaction $transaction)
    {
        if ($transaction->payment_status !== 'pending') {
            return redirect()
                ->route('admin.transactions.index')
                ->with('error', 'Hanya pesanan dengan status pending yang dapat dibatalkan.');
        }

        // Cancel di Midtrans (order_id terbaru, bisa dengan suffix retry)
        $orderId = $transaction->midtrans_retry_count > 0
            ? $transaction->order_code . '-R' . $transaction->midtrans_retry_count
            : $transaction->order_code;

        $this->midtrans->cancelTransaction($orderId);

        // Update status di database
        $transaction->update([
            'payment_status' => 'cancelled',
            'order_status'   => 'cancelled',
        ]);

        return redirect()
            ->route('admin.transactions.index')
            ->with('success', "Pesanan {$transaction->order_code} berhasil dibatalkan.");
    }
}
