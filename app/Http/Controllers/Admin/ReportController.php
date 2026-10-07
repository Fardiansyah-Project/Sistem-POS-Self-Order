<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\RawMaterialForecast;
use App\Http\Requests\ReportRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * GET /cms/admin/api/reports/sales
     * Export laporan penjualan (Download PDF/Excel - belum support JSON full karena outputnya file)
     */
    public function sales(ReportRequest $request)
    {
        $validated = $request->validated();

        $transactions = Transaction::with('details')
            ->where('payment_status', 'paid')
            ->whereDate('created_at', '>=', $validated['start_date'])
            ->whereDate('created_at', '<=', $validated['end_date'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalRevenue = $transactions->sum('total_amount');
        
        $pdf = Pdf::loadView('admin.reports.sales_pdf', [
            'transactions' => $transactions,
            'startDate'    => Carbon::parse($validated['start_date']),
            'endDate'      => Carbon::parse($validated['end_date']),
            'totalRevenue' => $totalRevenue
        ]);

        return $pdf->download('laporan-penjualan-' . date('Y-m-d') . '.pdf');
    }

    /**
     * GET /cms/admin/api/reports/forecast
     * Export laporan peramalan stok bahan (Download PDF)
     */
    public function forecast(ReportRequest $request)
    {
        $validated = $request->validated();

        $date = Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth();

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

        return $pdf->download('laporan-peramalan-' . $validated['month'] . '.pdf');
    }
}
