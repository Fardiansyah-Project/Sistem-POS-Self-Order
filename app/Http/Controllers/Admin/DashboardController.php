<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Ingredient;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. Ringkasan Hari Ini
        $ordersToday = Transaction::whereDate('created_at', $today)->count();
        $revenueToday = Transaction::whereDate('created_at', $today)->where('payment_status', 'paid')->sum('total_amount');
        
        // 2. Stok Kritis (Bahan Baku)
        $criticalIngredients = Ingredient::whereRaw('stock_quantity <= minimum_stock')->get();

        // 3. Produk Terlaris Bulan Ini
        $topProducts = DB::table('transaction_details as td')
            ->join('transactions as t', 't.id', '=', 'td.transaction_id')
            ->where('t.payment_status', 'paid')
            ->whereMonth('t.created_at', $today->month)
            ->select('td.product_name', DB::raw('SUM(td.quantity) as total_sold'))
            ->groupBy('td.product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // 4. Data Grafik Penjualan 7 Hari Terakhir
        $salesData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $total = Transaction::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            
            $salesData['labels'][] = $date->format('d M');
            $salesData['data'][] = $total;
        }

        return view('admin.dashboard', compact(
            'ordersToday', 
            'revenueToday', 
            'criticalIngredients', 
            'topProducts',
            'salesData'
        ));
    }
}
