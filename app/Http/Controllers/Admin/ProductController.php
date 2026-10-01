<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * GET /cms/admin/api/products
     */
    public function index(Request $request): JsonResponse
    {
        $products = Product::with('category')->orderBy('category_id')->orderBy('sort_order')->paginate(15);
        
        // Append image URL to each product for frontend
        $products->getCollection()->transform(function ($product) {
            $product->image_url = $product->image ? Storage::url($product->image) : null;
            return $product;
        });

        return response()->json($products);
    }

    /**
     * GET /cms/admin/api/products/{product}
     */
    public function show(Product $product): JsonResponse
    {
        $product->load('category');
        $product->image_url = $product->image ? Storage::url($product->image) : null;
        
        return response()->json([
            'data' => $product
        ]);
    }

    /**
     * POST /cms/admin/api/products
     */
    public function store(ProductRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk menu berhasil ditambahkan.',
            'data'    => $product
        ], 201);
    }

    /**
     * POST /cms/admin/api/products/{product}
     * Note: using POST for update to support multipart/form-data for file uploads
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        $validated = $request->validated();

        if ($request->name !== $product->name) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        }

        if ($request->hasFile('image')) {
            if ($product->image && ! Str::startsWith($product->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($validated['image']);
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data produk menu berhasil diperbarui.',
            'data'    => $product->fresh()
        ]);
    }

    /**
     * DELETE /cms/admin/api/products/{product}
     */
    public function destroy(Product $product): JsonResponse
    {
        try {
            // Hapus resep yang terkait terlebih dahulu
            $product->ingredients()->detach();
            
            if ($product->image && ! Str::startsWith($product->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($product->image);
            }
            
            $product->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus! Produk mungkin terikat pada transaksi historis.'
            ], 422);
        }
    }
}
