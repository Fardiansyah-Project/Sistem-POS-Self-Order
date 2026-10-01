@extends('layouts.admin')

@section('title', 'Kategori')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-4 pb-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-tags text-primary me-2"></i>Daftar Kategori</h6>
        <button class="btn btn-sm btn-primary shadow-sm" onclick="showFormModal()"><i class="bi bi-plus-lg me-1"></i> Tambah Kategori</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Nama Kategori</th>
                        <th>Icon</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th class="text-center">Jml Produk</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="categories-tbody">
                    <tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="cat-id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="cat-name" required placeholder="Contoh: Kopi, Snack">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Class Icon (Bootstrap Icons)</label>
                        <input type="text" class="form-control" name="icon" id="cat-icon" placeholder="Contoh: bi-cup-hot">
                        <div class="form-text">Biarkan kosong jika tidak pakai icon. Referensi: <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a></div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Urutan Tampil <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="sort_order" id="cat-sort" value="0" min="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Status <span class="text-danger">*</span></label>
                            <select class="form-select" name="is_active" id="cat-active">
                                <option value="1">Aktif</option>
                                <option value="0">Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/admin/categories.js"></script>
@endpush
