<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function __construct(private readonly StockService $stock) {}

    /**
     * Menampilkan antarmuka Kasir POS.
     */
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        
        $query = Product::with('category')->where('is_available', true);
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        }
        $products = $query->orderBy('sort_order')->get();

        return view('admin.pos.index', compact('categories', 'products'));
    }

    /**
     * Menyimpan pesanan dari POS.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:150',
            'table_number'  => 'nullable|string|max:50',
            'notes'         => 'nullable|string',
            'items'         => 'required|string', // JSON string from frontend
            'payment_type'  => 'required|string', // e.g., 'cash'
            'amount_paid'   => 'required|numeric|min:0',
        ]);

        $items = json_decode($validated['items'], true);
        
        if (empty($items) || !is_array($items)) {
            return back()->with('error', 'Keranjang belanja kosong atau format tidak valid.');
        }

        try {
            DB::transaction(function () use ($validated, $items) {
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
                
                // Simpan id order_code untuk mencetak struk
                session()->flash('print_receipt_id', $transaction->id);
            });

            return redirect()->route('admin.pos.index')->with('success', 'Pesanan berhasil dibuat dan lunas.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}
