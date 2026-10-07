<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\StockService;
use App\Http\Requests\PosRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function __construct(private readonly StockService $stock) {}

    /**
     * GET /cms/admin/api/pos/products
     * Ambil produk untuk POS, bisa difilter by category
     */
    public function products(Request $request): JsonResponse
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        $query = Product::with('category')->where('is_available', true);
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        }
        $products = $query->orderBy('sort_order')->get()->map(function ($product) {
            $product->image_url = $product->image ? \Illuminate\Support\Facades\Storage::url($product->image) : null;
            return $product;
        });

        return response()->json([
            'data' => [
                'categories' => $categories,
                'products'   => $products,
            ]
        ]);
    }

    /**
     * POST /cms/admin/api/pos/store
     * Menyimpan pesanan dari POS.
     */
    public function store(PosRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $items = json_decode($validated['items'], true);

        if (empty($items) || !is_array($items)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang belanja kosong atau format tidak valid.'
            ], 422);
        }

        try {
            $transactionData = DB::transaction(function () use ($validated, $items) {
                $subtotal = 0;
                $details = [];

                foreach ($items as $item) {
                    $product = Product::where('id', $item['id'])
                        ->where('is_available', true)
                        ->firstOrFail();

                    $itemSubtotal = $product->price * $item['quantity'];
                    $subtotal += $itemSubtotal;

                    $details[] = [
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'unit_price'   => $product->price,
                        'quantity'     => $item['quantity'],
                        'subtotal'     => $itemSubtotal,
                        'notes'        => $item['notes'] ?? null,
                    ];
                }

                $taxRate = 0.11; // 11% PPN (bisa sesuaikan config)
                $taxAmount = round($subtotal * $taxRate, 2);
                $totalAmount = $subtotal + $taxAmount;

                if ($validated['amount_paid'] < $totalAmount) {
                    throw new \Exception('Nominal pembayaran kurang dari total tagihan.');
                }

                $transaction = Transaction::create([
                    'order_code'     => Transaction::generateOrderCode(),
                    'customer_name'  => $validated['customer_name'],
                    'table_number'   => $validated['table_number'] ?? null,
                    'notes'          => $validated['notes'] ?? null,
                    'subtotal'       => $subtotal,
                    'tax_amount'     => $taxAmount,
                    'total_amount'   => $totalAmount,
                    'payment_status' => 'paid', // Karena kasir sudah menerima uang
                    'order_status'   => 'processing', // Langsung masuk dapur
                    'payment_type'   => $validated['payment_type'],
                    'paid_at'        => now(),
                ]);

                foreach ($details as $detail) {
                    $transaction->details()->create($detail);
                }

                // Potong stok
                $this->stock->deductForTransaction($transaction);

                return $transaction->load('details');
            });

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat dan lunas.',
                'data'    => [
                    'transaction' => $transactionData
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
