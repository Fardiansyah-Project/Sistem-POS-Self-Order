@extends('layouts.admin')

@section('title', 'Edit Produk Menu')

@section('content')
<div class="card border-0 shadow-sm max-w-3xl mx-auto">
    <div class="card-header bg-white pt-4 pb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Menu</h6>
    </div>
    <div class="card-body">
        <div id="loading-indicator" class="text-center py-4 text-muted">
            <div class="spinner-border spinner-border-sm me-2"></div> Memuat data produk...
        </div>

        <form id="product-form" class="d-none">
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label text-muted small fw-medium">Nama Produk / Menu</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted small fw-medium">Kategori</label>
                    <select name="category_id" id="category-select" class="form-select" required>
                        <option value="">-- Memuat Kategori --</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small fw-medium">Deskripsi Menu</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small fw-medium">Harga Jual (Rp)</label>
                    <input type="number" name="price" class="form-control" required min="0">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small fw-medium">Status Ketersediaan</label>
                    <select name="is_available" class="form-select" required>
                        <option value="1">Tersedia (Bisa Dipesan)</option>
                        <option value="0">Habis (Sembunyikan dari Menu)</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-4">
                    <label class="form-label text-muted small fw-medium">Gambar Produk (Opsional)</label>
                    <div id="current-image-preview" class="mb-2 d-none">
                        <img src="" class="rounded border p-1" style="max-height: 80px;" alt="Current Image">
                    </div>
                    <input type="file" name="image" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                    <div class="form-text small">Pilih file baru jika ingin mengganti gambar. Maksimal 2 MB.</div>
                </div>
                <div class="col-md-4 mb-4">
                    <label class="form-label text-muted small fw-medium">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" class="form-control" required min="0">
                </div>
            </div>

            <div class="d-flex gap-2 border-top pt-4">
                <a href="/cms/admin/products" class="btn btn-light border">Batal</a>
                <button type="submit" class="btn btn-primary px-4" id="btn-save"><i class="bi bi-save me-1"></i> Perbarui Produk</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const FORM_MODE = 'edit';
    const PRODUCT_ID = '{{ $id ?? "" }}';
</script>
<script src="/js/admin/product-form.js"></script>
@endpush