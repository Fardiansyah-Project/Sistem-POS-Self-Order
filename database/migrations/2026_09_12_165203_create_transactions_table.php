<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();        // e.g. KRC-20240912-001
            $table->string('customer_name');
            $table->string('table_number')->nullable();    // nomor meja (optional)
            $table->text('notes')->nullable();             // catatan pesanan customer
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled', 'expired'])
                  ->default('pending');
            $table->enum('order_status', ['waiting', 'processing', 'ready', 'completed'])
                  ->default('waiting');
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('snap_token')->nullable();
            $table->string('payment_type')->nullable();   // gopay, bank_transfer, dll.
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
