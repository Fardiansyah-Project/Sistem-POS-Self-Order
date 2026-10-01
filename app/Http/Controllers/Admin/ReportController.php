<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\RawMaterialForecast;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * GET /cms/admin/api/reports/sales
     * Export laporan penjualan (Download PDF/Excel - belum support JSON full karena outputnya file)
     */
    public function sales(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $transactions = Transaction::with('details')
            ->where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $transactions->sum('total_amount');
        
        $pdf = Pdf::loadView('admin.reports.sales_pdf', [
            'transactions' => $transactions,
            'startDate'    => Carbon::parse($request->start_date),
            'endDate'      => Carbon::parse($request->end_date),
            'totalRevenue' => $totalRevenue
        ]);

        return $pdf->download('laporan-penjualan-' . date('Y-m-d') . '.pdf');
    }

    /**
     * GET /cms/admin/api/reports/forecast
     * Export laporan peramalan stok bahan (Download PDF)
     */
    public function forecast(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $date = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();

        $forecasts = RawMaterialForecast::with('ingredient')
            ->whereYear('period_date', $date->year)
            ->whereMonth('period_date', $date->month)
            ->get();

        $pdf = Pdf::loadView('admin.reports.forecast_pdf', [
            'forecasts' => $forecasts,
            'month'     => $date->translatedFormat('F Y'),
            'startDate' => $date->copy()->startOfMonth(),
            'endDate'   => $date->copy()->endOfMonth(),
        ]);

        return $pdf->download('laporan-peramalan-' . $request->month . '.pdf');
    }
}
