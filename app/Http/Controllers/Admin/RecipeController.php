<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use App\Models\Product;
use App\Models\Recipe;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Tampilkan daftar komposisi resep untuk sebuah produk.
     */
    public function index(Product $product)
    {
        // Ambil resep (bahan baku) yang sudah ada di produk ini
        $product->load('recipes.ingredient');
        
        // Ambil semua bahan baku yang tersedia untuk dipilih di form
        $ingredients = Ingredient::orderBy('name')->get();

        return view('admin.recipes.index', compact('product', 'ingredients'));
    }

    /**
     * Tambahkan bahan baku baru ke dalam komposisi resep produk.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'ingredient_id'   => 'required|exists:ingredients,id',
            'quantity_needed' => 'required|numeric|min:0.01',
        ]);

        // Cek apakah bahan baku ini sudah ada di resep produk ini
        $exists = Recipe::where('product_id', $product->id)
                        ->where('ingredient_id', $request->ingredient_id)
                        ->first();

        if ($exists) {
            // Jika sudah ada, cukup tambahkan kuantitasnya
            $exists->update([
                'quantity_needed' => $exists->quantity_needed + $request->quantity_needed
            ]);
        } else {
            // Jika belum ada, buat baru
            Recipe::create([
                'product_id'      => $product->id,
                'ingredient_id'   => $request->ingredient_id,
                'quantity_needed' => $request->quantity_needed,
            ]);
        }

        return redirect()->back()->with('success', 'Bahan baku berhasil ditambahkan ke resep.');
    }

    /**
     * Hapus bahan baku dari komposisi resep produk.
     */
    public function destroy(Product $product, Recipe $recipe)
    {
        // Pastikan resep ini benar-benar milik produk yang sesuai
        if ($recipe->product_id !== $product->id) {
            abort(404);
        }

        $recipe->delete();

        return redirect()->back()->with('success', 'Bahan baku berhasil dihapus dari resep.');
    }
}
