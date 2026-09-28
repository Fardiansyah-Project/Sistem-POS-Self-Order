<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\RawMaterialForecast;
use App\Exports\SalesExport;
use App\Exports\ForecastExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function sales(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'format'     => 'required|in:pdf,excel',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate   = Carbon::parse($request->end_date)->endOfDay();

        $transactions = Transaction::with('details.product')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->format === 'excel') {
            return Excel::download(new SalesExport($transactions, $startDate, $endDate), 'laporan_penjualan_' . date('Ymd') . '.xlsx');
        }

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.sales_pdf', compact('transactions', 'startDate', 'endDate'));
            return $pdf->download('laporan_penjualan_' . date('Ymd') . '.pdf');
        }
    }

    public function forecast(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'format'     => 'required|in:pdf,excel',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate   = Carbon::parse($request->end_date)->endOfDay();

        $forecasts = RawMaterialForecast::with('ingredient')
            ->whereBetween('period_date', [$startDate, $endDate])
            ->orderBy('period_date', 'desc')
            ->get();

        if ($request->format === 'excel') {
            return Excel::download(new ForecastExport($forecasts, $startDate, $endDate), 'laporan_peramalan_' . date('Ymd') . '.xlsx');
        }

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadView('admin.reports.forecast_pdf', compact('forecasts', 'startDate', 'endDate'));
            return $pdf->download('laporan_peramalan_' . date('Ymd') . '.pdf');
        }
    }
}
