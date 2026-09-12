<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:categories,name',
            'icon'       => 'nullable|string|max:50',
            'sort_order' => 'required|integer|min:0',
            'is_active'  => 'required|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100|unique:categories,name,' . $category->id,
            'icon'       => 'nullable|string|max:50',
            'sort_order' => 'required|integer|min:0',
            'is_active'  => 'required|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Gagal menghapus! Kategori masih memiliki produk.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
