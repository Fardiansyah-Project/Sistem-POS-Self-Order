<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * GET /cms/admin/api/categories
     * Ambil semua kategori beserta jumlah produk.
     */
    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    /**
     * GET /cms/admin/api/categories/{category}
     * Ambil detail satu kategori (untuk form edit).
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json([
            'data' => $category,
        ]);
    }

    /**
     * POST /cms/admin/api/categories
     * Simpan kategori baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:categories,name',
            'icon'       => 'nullable|string|max:50',
            'sort_order' => 'required|integer|min:0',
            'is_active'  => 'required|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil ditambahkan.',
            'data'    => $category,
        ], 201);
    }

    /**
     * PUT /cms/admin/api/categories/{category}
     * Perbarui data kategori.
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:categories,name,' . $category->id,
            'icon'       => 'nullable|string|max:50',
            'sort_order' => 'required|integer|min:0',
            'is_active'  => 'required|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui.',
            'data'    => $category->fresh(),
        ]);
    }

    /**
     * DELETE /cms/admin/api/categories/{category}
     * Hapus kategori.
     */
    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus! Kategori masih memiliki produk.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}
