<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    /**
     * GET /cms/admin/api/ingredients?page=1&all=true
     * Ambil daftar bahan baku. Support pagination dan mode all (untuk dropdown).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Ingredient::orderBy('name');

        // Jika parameter all=true, kembalikan semua tanpa pagination (untuk dropdown select)
        if ($request->boolean('all')) {
            return response()->json([
                'data' => $query->get(),
            ]);
        }

        // Default: paginated
        $ingredients = $query->paginate(15);

        return response()->json($ingredients);
    }

    /**
     * GET /cms/admin/api/ingredients/{ingredient}
     * Ambil detail satu bahan baku (untuk form edit).
     */
    public function show(Ingredient $ingredient): JsonResponse
    {
        return response()->json([
            'data' => $ingredient,
        ]);
    }

    /**
     * POST /cms/admin/api/ingredients
     * Simpan bahan baku baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100|unique:ingredients,name',
            'unit'           => 'required|string|max:20',
            'stock_quantity' => 'required|numeric|min:0',
            'minimum_stock'  => 'required|numeric|min:0',
        ]);

        $ingredient = Ingredient::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku berhasil ditambahkan.',
            'data'    => $ingredient,
        ], 201);
    }

    /**
     * PUT /cms/admin/api/ingredients/{ingredient}
     * Perbarui data bahan baku.
     */
    public function update(Request $request, Ingredient $ingredient): JsonResponse
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100|unique:ingredients,name,' . $ingredient->id,
            'unit'           => 'required|string|max:20',
            'stock_quantity' => 'required|numeric|min:0',
            'minimum_stock'  => 'required|numeric|min:0',
        ]);

        $ingredient->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data bahan baku berhasil diperbarui.',
            'data'    => $ingredient->fresh(),
        ]);
    }

    /**
     * DELETE /cms/admin/api/ingredients/{ingredient}
     * Hapus bahan baku.
     */
    public function destroy(Ingredient $ingredient): JsonResponse
    {
        // Cegah hapus jika masih dipakai di resep
        try {
            $ingredient->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bahan baku berhasil dihapus.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus bahan baku, mungkin masih terikat dengan resep produk.',
            ], 422);
        }
    }
}
