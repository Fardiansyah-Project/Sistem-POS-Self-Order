<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit');                   // gram, ml, pcs, sachet, dll.
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->decimal('minimum_stock', 10, 2)->default(0); // threshold stok kritis
            $table->decimal('cost_per_unit', 10, 2)->default(0); // biaya per satuan (HPP)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
