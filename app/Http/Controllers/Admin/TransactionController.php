<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
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
}
