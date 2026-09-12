<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * GET /api/v1/products
     * Ambil semua produk tersedia, dengan filter kategori opsional.
     * Query params: ?category=espresso-based
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::available()
            ->with('category:id,name,slug')
            ->select('id', 'category_id', 'name', 'slug', 'description', 'price', 'image', 'sort_order');

        // Filter by category slug
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        $products = $query->get()->map(function ($product) {
            return [
                'id'          => $product->id,
                'name'        => $product->name,
                'slug'        => $product->slug,
                'description' => $product->description,
                'price'       => (float) $product->price,
                'image_url'   => $product->image_url,
                'category'    => $product->category,
            ];
        });

        return response()->json(['data' => $products]);
    }

    /**
     * GET /api/v1/products/{slug}
     * Ambil detail satu produk termasuk resep bahan baku.
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::available()
            ->where('slug', $slug)
            ->with(['category:id,name,slug', 'ingredients:id,name,unit'])
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id'          => $product->id,
                'name'        => $product->name,
                'slug'        => $product->slug,
                'description' => $product->description,
                'price'       => (float) $product->price,
                'image_url'   => $product->image_url,
                'category'    => $product->category,
            ],
        ]);
    }
}
