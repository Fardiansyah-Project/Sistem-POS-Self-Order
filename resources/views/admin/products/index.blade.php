@extends('layouts.admin')

@section('title', 'Produk Menu')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-4 pb-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Daftar Produk / Menu</h6>
        <a href="/cms/admin/products/create" class="btn btn-sm btn-primary shadow-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Menu</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Info Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="products-tbody">
                    <tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white pt-4 pb-3 border-top-0 d-flex justify-content-end" id="pagination-container">
        <!-- Pagination goes here -->
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/admin/products.js"></script>
@endpush
