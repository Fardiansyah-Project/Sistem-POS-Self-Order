<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecipeController extends Controller
{
    /**
     * GET /cms/admin/api/products/{product}/recipes
     * Ambil daftar resep dari suatu produk
     */
    public function index(Product $product): JsonResponse
    {
        $recipes = $product->ingredients()->withPivot('quantity_needed')->get();

        return response()->json([
            'data' => [
                'product' => [
                    'id'   => $product->id,
                    'name' => $product->name,
                ],
                'recipes' => $recipes
            ]
        ]);
    }

    /**
     * POST /cms/admin/api/products/{product}/recipes
     * Tambahkan bahan baku ke dalam resep produk
     */
    public function store(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'ingredient_id'   => 'required|exists:ingredients,id',
            'quantity_needed' => 'required|numeric|min:0.01',
        ]);

        // Cek jika sudah ada, maka update
        $existing = DB::table('recipes')
            ->where('product_id', $product->id)
            ->where('ingredient_id', $request->ingredient_id)
            ->first();

        if ($existing) {
            $product->ingredients()->updateExistingPivot($request->ingredient_id, [
                'quantity_needed' => $request->quantity_needed,
                'updated_at'      => now(),
            ]);
            $msg = 'Bahan baku di resep berhasil diperbarui.';
        } else {
            $product->ingredients()->attach($request->ingredient_id, [
                'quantity_needed' => $request->quantity_needed,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            $msg = 'Bahan baku berhasil ditambahkan ke resep.';
        }

        return response()->json([
            'success' => true,
            'message' => $msg
        ]);
    }

    /**
     * DELETE /cms/admin/api/products/{product}/recipes/{ingredient_id}
     * Hapus bahan baku dari resep produk
     */
    public function destroy(Product $product, $ingredient_id): JsonResponse
    {
        $product->ingredients()->detach($ingredient_id);

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku berhasil dihapus dari resep.'
        ]);
    }
}
