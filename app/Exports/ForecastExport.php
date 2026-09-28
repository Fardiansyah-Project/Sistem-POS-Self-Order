<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ForecastExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $forecasts;
    protected $startDate;
    protected $endDate;

    public function __construct($forecasts, $startDate, $endDate)
    {
        $this->forecasts = $forecasts;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('admin.reports.forecast_excel', [
            'forecasts' => $this->forecasts,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 14]], // Header title
            3    => ['font' => ['bold' => true]], // Table Header
        ];
    }
}
