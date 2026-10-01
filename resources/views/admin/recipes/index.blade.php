@extends('layouts.admin')

@section('title', 'Atur Resep Menu')

@section('content')
<div class="row g-4" id="recipe-container">
    <!-- Informasi Produk -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white pt-4 pb-3">
                <h6 class="fw-bold mb-0">Informasi Produk</h6>
            </div>
            <div class="card-body text-center pt-4" id="product-info">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted mt-2">Memuat data produk...</p>
            </div>
            <div class="card-footer bg-white pt-3 pb-4 border-top-0 text-center">
                <a href="/cms/admin/products" class="btn btn-outline-secondary btn-sm w-100">
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
                <form id="recipe-form">
                    <div class="row align-items-end">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label text-muted small fw-medium">Pilih Bahan Baku</label>
                            <select name="ingredient_id" class="form-select" required id="ingredient-select">
                                <option value="">-- Memuat Bahan Baku --</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="form-label text-muted small fw-medium">Takaran (Per 1 Porsi)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="quantity_needed" id="quantity-input" class="form-control" required min="0.01">
                                <span class="input-group-text bg-light" id="unit-label">-</span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100" id="btn-add-recipe"><i class="bi bi-plus-lg"></i> Tambah</button>
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
                        <tbody id="recipes-tbody">
                            <tr><td colspan="3" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const PRODUCT_ID = '{{ $productId }}'; // Injected from route param
</script>
<script src="/js/admin/recipes.js"></script>
@endpush
