<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * GET /api/v1/categories
     * Ambil semua kategori aktif beserta jumlah produk tersedia.
     */
    public function index(): JsonResponse
    {
        $categories = Category::active()
            ->withCount(['products' => fn($q) => $q->where('is_available', true)])
            ->get(['id', 'name', 'slug', 'icon', 'sort_order', 'products_count']);

        return response()->json([
            'data' => $categories,
        ]);
    }
}
