<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\RawMaterialForecast;
use App\Services\ForecastService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForecastController extends Controller
{
    public function __construct(private readonly ForecastService $forecastService) {}

    /**
     * GET /cms/admin/api/forecast?ingredient_id=X
     * Ambil data peramalan WMA untuk satu bahan baku.
     */
    public function data(Request $request): JsonResponse
    {
        $ingredients = Ingredient::orderBy('name')->get(['id', 'name', 'unit']);
        $selectedIngredientId = $request->get('ingredient_id', $ingredients->first()->id ?? null);
        
        $forecasts = [];
        $chartData = null;

        if ($selectedIngredientId) {
            // Ambil histori 12 bulan terakhir dari WMA cache di database
            $forecasts = RawMaterialForecast::where('ingredient_id', $selectedIngredientId)
                            ->orderBy('period_date', 'desc')
                            ->take(12)
                            ->get();

            // Siapkan data untuk Chart.js (di reverse agar kronologis dari kiri ke kanan)
            if ($forecasts->isNotEmpty()) {
                $latest = $forecasts->first();
                $histData = $latest->historical_data ?? [];
                
                // Labels: YYYY-MM
                $labels = array_keys($histData);
                // Actual Data
                $actuals = array_values($histData);
                
                // Karena kita tidak menyimpan seluruh forecast array di DB (hanya hasil bulan depan),
                // kita run ulang kalkulasi ringan untuk render chart (hanya untuk visualisasi).
                $weights = $latest->wma_weights ?? [1,2,3];
                $periods = count($weights);
                $forecastPlot = array_fill(0, $periods, null); // N bulan pertama kosong karena butuh N data awal

                for ($i = $periods; $i <= count($actuals); $i++) {
                    $slice = array_slice($actuals, $i - $periods, $periods);
                    $forecastPlot[] = $this->forecastService->calculate($slice, $weights);
                }

                // Tambahkan bulan prediksi masa depan ke label
                $nextMonth = \Carbon\Carbon::parse(end($labels).'-01')->addMonth()->format('Y-m');
                $labels[] = $nextMonth . ' (Prediksi)';
                
                // Actual plot tidak punya data untuk bulan depan
                $actualPlot = $actuals;
                $actualPlot[] = null;

                $chartData = [
                    'labels'        => $labels,
                    'actuals'       => $actualPlot,
                    'forecasts'     => $forecastPlot,
                    'weights'       => implode(', ', $weights),
                    'mae'           => $latest->mean_absolute_error,
                    'next_forecast' => $latest->forecasted_amount,
                ];
            }
        }

        return response()->json([
            'data' => [
                'ingredients'            => $ingredients,
                'selected_ingredient_id' => $selectedIngredientId,
                'forecasts'              => $forecasts,
                'chart_data'             => $chartData,
            ],
        ]);
    }

    /**
     * POST /cms/admin/api/forecast/run
     * Jalankan peramalan WMA untuk semua bahan baku.
     */
    public function run(Request $request): JsonResponse
    {
        $request->validate([
            'weights' => 'required|string'
        ]);

        $weightsStr = explode(',', $request->weights);
        $weights = array_map('intval', $weightsStr);

        try {
            $this->forecastService->forecastAllIngredients($weights);

            return response()->json([
                'success' => true,
                'message' => 'Peramalan WMA berhasil dijalankan dan diperbarui untuk semua bahan baku.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menjalankan peramalan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
