<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ForecastService;
use App\Models\Ingredient;
use App\Models\RawMaterialForecast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ForecastController extends Controller
{
    public function __construct(private readonly ForecastService $forecastService) {}

    /**
     * GET /api/v1/forecast/wma
     * Jalankan peramalan WMA untuk semua bahan baku (admin only).
     * Query params:
     *   ?weights=1,2,3   (default: 1,2,3)
     *   ?run=true        (jalankan ulang kalkulasi, default: ambil dari cache DB)
     */
    public function index(Request $request): JsonResponse
    {
        $weightsParam = $request->get('weights', '1,2,3');
        $weights      = array_map('intval', explode(',', $weightsParam));
        $run          = $request->boolean('run', false);

        // Validasi weights
        if (count($weights) < 2 || count($weights) > 6) {
            return response()->json([
                'message' => 'Jumlah weight harus antara 2-6 periode.',
            ], 422);
        }

        if ($run) {
            // Jalankan ulang kalkulasi WMA untuk semua bahan baku
            $results = $this->forecastService->forecastAllIngredients($weights);
        } else {
            // Ambil hasil forecast terbaru dari database (tidak run ulang)
            $results = RawMaterialForecast::with('ingredient:id,name,unit,stock_quantity,minimum_stock')
                ->orderByDesc('period_date')
                ->get()
                ->groupBy('ingredient_id')
                ->map(fn($group) => $group->first())
                ->values()
                ->map(function ($forecast) {
                    return [
                        'ingredient_id'     => $forecast->ingredient_id,
                        'ingredient_name'   => $forecast->ingredient->name,
                        'unit'              => $forecast->ingredient->unit,
                        'stock_quantity'    => (float) $forecast->ingredient->stock_quantity,
                        'minimum_stock'     => (float) $forecast->ingredient->minimum_stock,
                        'period_date'       => $forecast->period_date->format('Y-m'),
                        'forecasted_amount' => (float) $forecast->forecasted_amount,
                        'actual_usage'      => $forecast->actual_usage ? (float) $forecast->actual_usage : null,
                        'wma_weights'       => $forecast->wma_weights,
                        'mae'               => $forecast->mean_absolute_error ? (float) $forecast->mean_absolute_error : null,
                        'historical_data'   => $forecast->historical_data,
                    ];
                });

            return response()->json(['data' => $results]);
        }

        return response()->json([
            'message'  => 'Peramalan WMA berhasil dijalankan.',
            'weights'  => $weights,
            'data'     => $results,
        ]);
    }
}
