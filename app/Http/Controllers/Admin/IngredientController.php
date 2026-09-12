<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::orderBy('name')->paginate(15);
        return view('admin.ingredients.index', compact('ingredients'));
    }

    public function create()
    {
        return view('admin.ingredients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100|unique:ingredients,name',
            'unit'           => 'required|string|max:20',
            'stock_quantity' => 'required|numeric|min:0',
            'minimum_stock'  => 'required|numeric|min:0',
        ]);

        Ingredient::create($validated);
        return redirect()->route('admin.ingredients.index')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function edit(Ingredient $ingredient)
    {
        return view('admin.ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100|unique:ingredients,name,' . $ingredient->id,
            'unit'           => 'required|string|max:20',
            'stock_quantity' => 'required|numeric|min:0',
            'minimum_stock'  => 'required|numeric|min:0',
        ]);

        $ingredient->update($validated);
        return redirect()->route('admin.ingredients.index')->with('success', 'Data bahan baku berhasil diperbarui.');
    }

    public function destroy(Ingredient $ingredient)
    {
        // Cegah hapus jika masih dipakai di resep (opsional, tergantung logic foreign key constraint)
        try {
            $ingredient->delete();
            return redirect()->route('admin.ingredients.index')->with('success', 'Bahan baku berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('admin.ingredients.index')->with('error', 'Gagal menghapus bahan baku, mungkin masih terikat dengan resep produk.');
        }
    }
}
