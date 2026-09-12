@extends('layouts.admin')

@section('title', 'Atur Resep Menu')

@section('content')
<div class="row g-4">
    <!-- Informasi Produk -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white pt-4 pb-3">
                <h6 class="fw-bold mb-0">Informasi Produk</h6>
            </div>
            <div class="card-body text-center pt-4">
                @if($product->image)
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="rounded shadow-sm mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="bg-light rounded mx-auto d-flex align-items-center justify-content-center text-muted mb-3" style="width: 120px; height: 120px;">
                        <i class="bi bi-image display-4"></i>
                    </div>
                @endif
                <h5 class="fw-bold mb-1">{{ $product->name }}</h5>
                <span class="badge bg-secondary rounded-pill mb-3">{{ $product->category->name ?? 'Tanpa Kategori' }}</span>
                
                <p class="text-muted small mb-3">{{ $product->description ?: 'Tidak ada deskripsi.' }}</p>
                <div class="fw-bold fs-5 text-brand" style="color: #c97d20;">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </div>
            </div>
            <div class="card-footer bg-white pt-3 pb-4 border-top-0 text-center">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Menu
                </a>
            </div>
        </div>
    </div>

    <!-- Manajemen Komposisi Resep -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white pt-4 pb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Bahan Baku ke Resep</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.recipes.store', $product->id) }}" method="POST">
                    @csrf
                    <div class="row align-items-end">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label text-muted small fw-medium">Pilih Bahan Baku</label>
                            <select name="ingredient_id" class="form-select @error('ingredient_id') is-invalid @enderror" required id="ingredient-select">
                                <option value="">-- Pilih Bahan Baku --</option>
                                @foreach($ingredients as $ing)
                                    <option value="{{ $ing->id }}" data-unit="{{ $ing->unit }}">{{ $ing->name }}</option>
                                @endforeach
                            </select>
                            @error('ingredient_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label text-muted small fw-medium">Takaran (Per 1 Porsi)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="quantity_needed" class="form-control @error('quantity_needed') is-invalid @enderror" value="{{ old('quantity_needed') }}" required>
                                <span class="input-group-text bg-light" id="unit-label">-</span>
                            </div>
                            @error('quantity_needed') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i> Tambah</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Komposisi Resep Saat Ini -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white pt-4 pb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-list-nested text-info me-2"></i>Komposisi Resep Saat Ini</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nama Bahan Baku</th>
                                <th>Takaran (Per Porsi)</th>
                                <th class="text-center pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->recipes as $recipe)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $recipe->ingredient->name }}</td>
                                <td>
                                    <span class="fw-bold">{{ floatval($recipe->quantity_needed) }}</span> 
                                    <span class="text-muted">{{ $recipe->ingredient->unit }}</span>
                                </td>
                                <td class="text-center pe-4">
                                    <form action="{{ route('admin.recipes.destroy', [$product->id, $recipe->id]) }}" method="POST" onsubmit="return confirm('Hapus bahan ini dari resep?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus dari Resep"><i class="bi bi-x-lg"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox d-block fs-2 mb-2 opacity-50"></i>
                                    Produk ini belum memiliki komposisi bahan baku.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update label satuan saat bahan baku dipilih
    document.getElementById('ingredient-select').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var unit = selectedOption.getAttribute('data-unit') || '-';
        document.getElementById('unit-label').innerText = unit;
    });
</script>
@endsection
