<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    /**
     * Tampilkan halaman Monitor Dapur
     */
    public function index()
    {
        // Hanya ambil transaksi yang sudah dibayar, dan statusnya belum completed/cancelled
        $activeOrders = Transaction::with('details')
            ->where('payment_status', 'paid')
            ->whereIn('order_status', ['waiting', 'processing', 'ready'])
            ->orderBy('paid_at', 'asc') // Yang bayar duluan, diproses duluan
            ->get();

        return view('admin.kitchen.index', compact('activeOrders'));
    }

    /**
     * Update status operasional dapur untuk pesanan tertentu.
     */
    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'order_status' => 'required|in:processing,ready,completed'
        ]);

        $transaction->update([
            'order_status' => $request->order_status
        ]);

        $message = match($request->order_status) {
            'processing' => 'Pesanan sedang diproses di dapur.',
            'ready'      => 'Pesanan siap untuk diambil pelanggan.',
            'completed'  => 'Pesanan telah selesai/diambil.',
            default      => 'Status pesanan diperbarui.'
        };

        return redirect()->back()->with('success', $message);
    }
}
