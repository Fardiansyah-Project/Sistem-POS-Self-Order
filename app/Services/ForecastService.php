<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\RawMaterialForecast;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * ForecastService — Implementasi Weighted Moving Average (WMA) untuk peramalan bahan baku.
 *
 * Formula WMA:
 *   WMA = Σ(W_t × X_t) / Σ(W_t)
 *
 * Keterangan:
 *   W_t = bobot periode ke-t
 *   X_t = penggunaan bahan baku pada periode ke-t
 *   Periode lebih baru mendapat bobot lebih tinggi.
 */
class ForecastService
{
    /**
     * Hitung nilai WMA dari data historis dan bobot yang diberikan.
     *
     * @param  array $data    Data historis penggunaan [oldest → newest], e.g. [120.5, 145.0, 130.0]
     * @param  array $weights Bobot per periode [oldest → newest], e.g. [1, 2, 3]
     * @return float          Hasil peramalan WMA
     *
     * @throws \InvalidArgumentException jika panjang $data dan $weights tidak sama atau kosong
     */
    public function calculate(array $data, array $weights): float
    {
        if (empty($data) || empty($weights)) {
            throw new \InvalidArgumentException('Data dan weights tidak boleh kosong.');
        }

        if (count($data) !== count($weights)) {
            throw new \InvalidArgumentException(
                'Jumlah data (' . count($data) . ') harus sama dengan jumlah weights (' . count($weights) . ').'
            );
        }

        $weightedSum = 0.0;
        $totalWeight = 0.0;

        // Σ(W_t × X_t)
        foreach ($data as $index => $value) {
            $weightedSum += $weights[$index] * $value;
            $totalWeight += $weights[$index];
        }

        // Hindari division by zero
        if ($totalWeight == 0) {
            return 0.0;
        }

        // WMA = Σ(W_t × X_t) / Σ(W_t)
        return round($weightedSum / $totalWeight, 3);
    }

    /**
     * Hitung Mean Absolute Error (MAE) untuk mengukur akurasi peramalan.
     *
     * MAE = Σ|actual - forecast| / n
     *
     * @param  array $actuals   Nilai aktual
     * @param  array $forecasts Nilai peramalan
     * @return float            Nilai MAE
     */
    public function calculateMAE(array $actuals, array $forecasts): float
    {
        if (count($actuals) !== count($forecasts) || empty($actuals)) {
            return 0.0;
        }

        $totalError = 0.0;
        $n          = count($actuals);

        foreach ($actuals as $index => $actual) {
            $totalError += abs($actual - $forecasts[$index]);
        }

        return round($totalError / $n, 4);
    }

    /**
     * Ambil data penggunaan bahan baku per bulan dari histori transaksi.
     * Menghitung total penggunaan dengan mengalikan quantity terjual × quantity_needed di resep.
     *
     * @param  int $ingredientId  ID bahan baku
     * @param  int $monthsBack    Jumlah bulan ke belakang yang diambil
     * @return array              Array asosiatif ['YYYY-MM' => total_usage]
     */
    public function getMonthlyUsageHistory(int $ingredientId, int $monthsBack = 6): array
    {
        if ($monthsBack < 1) {
            return [];
        }

        // Histori harus berakhir pada bulan berjalan agar penjualan terbaru
        // ikut menjadi dasar prediksi bulan berikutnya.
        $startDate = Carbon::now('Asia/Makassar')->subMonths($monthsBack - 1)->startOfMonth();

        // Join transaction_details → recipes untuk menghitung actual ingredient usage
        $usageData = DB::table('transaction_details as td')
            ->join('transactions as t', 't.id', '=', 'td.transaction_id')
            ->join('recipes as r', function ($join) use ($ingredientId) {
                $join->on('r.product_id', '=', 'td.product_id')
                     ->where('r.ingredient_id', '=', $ingredientId);
            })
            ->where('t.payment_status', 'paid')
            ->where('t.created_at', '>=', $startDate)
            ->select(
                DB::raw("DATE_FORMAT(t.created_at, '%Y-%m') as month"),
                DB::raw('SUM(td.quantity * r.quantity_needed) as total_usage')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total_usage', 'month')
            ->toArray();

        // Isi bulan yang tidak ada transaksi dengan 0
        $result = [];
        for ($i = $monthsBack - 1; $i >= 0; $i--) {
            $month          = Carbon::now('Asia/Makassar')->subMonths($i)->format('Y-m');
            $result[$month] = (float) ($usageData[$month] ?? 0);
        }

        return $result;
    }

    /**
     * Generate peramalan WMA untuk satu bahan baku pada periode berikutnya.
     * Hasil disimpan ke tabel raw_material_forecasts.
     *
     * @param  int   $ingredientId  ID bahan baku
     * @param  array $weights       Bobot WMA, default [1, 2, 3] untuk 3 periode
     * @return array                Data hasil peramalan + metadata
     */
    public function forecastIngredient(int $ingredientId, array $weights = [1, 2, 3]): array
    {
        $periods         = count($weights);
        $monthlyHistory  = $this->getMonthlyUsageHistory($ingredientId, $periods + 3); // ambil lebih untuk MAE
        $historyValues   = array_values($monthlyHistory);
        $historyKeys     = array_keys($monthlyHistory);

        // Ambil data $periods terakhir sebagai input WMA
        $inputData = array_slice($historyValues, -$periods);

        // Hitung WMA untuk bulan berikutnya
        $forecastedAmount = $this->calculate($inputData, $weights);

        // Hitung MAE menggunakan data historis (validasi silang sederhana)
        $mae = 0.0;
        if (count($historyValues) > $periods) {
            $maeForecasts = [];
            $maeActuals   = [];
            for ($i = $periods; $i < count($historyValues); $i++) {
                $slice           = array_slice($historyValues, $i - $periods, $periods);
                $maeForecasts[]  = $this->calculate($slice, $weights);
                $maeActuals[]    = $historyValues[$i];
            }
            $mae = $this->calculateMAE($maeActuals, $maeForecasts);
        }

        // Target periode peramalan = bulan depan
        $periodDate = Carbon::now('Asia/Makassar')->addMonth()->startOfMonth()->format('Y-m-d');

        // Simpan atau update hasil peramalan ke database
        $forecast = RawMaterialForecast::updateOrCreate(
            [
                'ingredient_id' => $ingredientId,
                'period_date'   => $periodDate,
            ],
            [
                'forecasted_amount' => $forecastedAmount,
                'historical_data'   => array_combine($historyKeys, $historyValues),
                'wma_weights'       => $weights,
                'mean_absolute_error' => $mae,
            ]
        );

        return [
            'ingredient_id'     => $ingredientId,
            'period_date'       => $periodDate,
            'historical_data'   => array_combine($historyKeys, $historyValues),
            'wma_weights'       => $weights,
            'forecasted_amount' => $forecastedAmount,
            'mae'               => $mae,
            'forecast_id'       => $forecast->id,
        ];
    }

    /**
     * Generate peramalan untuk SEMUA bahan baku sekaligus.
     *
     * @param  array $weights Bobot WMA yang digunakan
     * @return array          Array hasil peramalan per bahan baku
     */
    public function forecastAllIngredients(array $weights = [1, 2, 3]): array
    {
        $ingredients = Ingredient::all();
        $results     = [];

        foreach ($ingredients as $ingredient) {
            try {
                $results[] = array_merge(
                    $this->forecastIngredient($ingredient->id, $weights),
                    ['ingredient_name' => $ingredient->name, 'unit' => $ingredient->unit]
                );
            } catch (\Exception $e) {
                $results[] = [
                    'ingredient_id'   => $ingredient->id,
                    'ingredient_name' => $ingredient->name,
                    'error'           => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
