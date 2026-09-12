<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_material_forecasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->date('period_date');                 // bulan yang diramal (YYYY-MM-01)
            $table->decimal('actual_usage', 10, 3)->nullable();     // realisasi penggunaan
            $table->decimal('forecasted_amount', 10, 3);            // hasil WMA
            $table->json('historical_data');             // array data historis yang digunakan
            $table->json('wma_weights');                 // bobot WMA yang digunakan, e.g. [1,2,3]
            $table->decimal('mean_absolute_error', 10, 4)->nullable(); // akurasi peramalan
            $table->timestamps();

            // Satu bahan baku hanya punya satu forecast per periode
            $table->unique(['ingredient_id', 'period_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_material_forecasts');
    }
};
