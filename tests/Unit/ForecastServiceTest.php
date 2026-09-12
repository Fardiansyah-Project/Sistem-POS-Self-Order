<?php

namespace Tests\Unit;

use App\Services\ForecastService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ForecastServiceTest extends TestCase
{
    private ForecastService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ForecastService();
    }

    public function test_wma_calculation_with_valid_data()
    {
        // Contoh: Data penggunaan 3 bulan terakhir [Bulan 1, Bulan 2, Bulan 3]
        $data = [100.0, 120.0, 130.0];
        
        // Bobot: [w1=1, w2=2, w3=3] => total bobot = 6
        // WMA = (100*1 + 120*2 + 130*3) / 6
        // WMA = (100 + 240 + 390) / 6
        // WMA = 730 / 6 = 121.667
        $weights = [1, 2, 3];

        $result = $this->service->calculate($data, $weights);

        $this->assertEquals(121.667, $result);
    }

    public function test_wma_calculation_throws_error_on_mismatched_arrays()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Jumlah data (2) harus sama dengan jumlah weights (3).');

        $data = [100.0, 120.0]; // Hanya 2 data
        $weights = [1, 2, 3];   // Tapi 3 bobot

        $this->service->calculate($data, $weights);
    }

    public function test_wma_calculation_throws_error_on_empty_arrays()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Data dan weights tidak boleh kosong.');

        $this->service->calculate([], []);
    }

    public function test_mae_calculation_is_accurate()
    {
        // MAE = Σ|actual - forecast| / n
        $actuals = [120.0, 130.0, 140.0];
        $forecasts = [115.0, 132.0, 138.0];
        
        // Errors: |120-115|=5, |130-132|=2, |140-138|=2
        // Total = 9
        // MAE = 9 / 3 = 3.0

        $mae = $this->service->calculateMAE($actuals, $forecasts);

        $this->assertEquals(3.0, $mae);
    }
}
