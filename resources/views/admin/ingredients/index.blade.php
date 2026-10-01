@extends('layouts.admin')

@section('title', 'Bahan Baku')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-4 pb-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-basket2 text-primary me-2"></i>Daftar Bahan Baku (Inventory)</h6>
        <button class="btn btn-sm btn-primary shadow-sm" onclick="showFormModal()"><i class="bi bi-plus-lg me-1"></i> Tambah Bahan</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Bahan Baku</th>
                        <th>Satuan (Unit)</th>
                        <th>Sisa Stok Saat Ini</th>
                        <th>Batas Stok Kritis (Minimum)</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ingredients-tbody">
                    <tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white pt-4 pb-3 border-top-0 d-flex justify-content-end" id="pagination-container">
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="ingredientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Tambah Bahan Baku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="ingredientForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="ing-id">
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nama Bahan Baku <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="ing-name" required placeholder="Contoh: Biji Kopi Arabica">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Satuan (Unit) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="unit" id="ing-unit" required placeholder="Contoh: Gram, Ml, Pcs">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Stok Saat Ini <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" name="stock_quantity" id="ing-stock" required min="0">
                                <span class="input-group-text unit-label">-</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">Batas Kritis <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" name="minimum_stock" id="ing-min" required min="0">
                                <span class="input-group-text unit-label">-</span>
                            </div>
                            <div class="form-text small">Minimal stok untuk peringatan.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btn-save">Simpan Bahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/admin/ingredients.js"></script>
@endpush
