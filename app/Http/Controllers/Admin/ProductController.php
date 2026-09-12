<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderBy('category_id')->orderBy('sort_order')->paginate(15);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:150',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order'   => 'required|integer|min:0',
            'is_available' => 'required|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);
        return redirect()->route('admin.products.index')->with('success', 'Produk menu berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:150',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'sort_order'   => 'required|integer|min:0',
            'is_available' => 'required|boolean',
        ]);

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
        return redirect()->route('admin.products.index')->with('success', 'Data produk menu berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        try {
            // Hapus resep yang terkait terlebih dahulu
            $product->ingredients()->detach();
            $product->delete();
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.products.index')->with('error', 'Gagal menghapus! Produk mungkin terikat pada transaksi historis.');
        }
    }
}
