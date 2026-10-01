@extends('layouts.admin')

@section('title', 'Data Transaksi')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-4 pb-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-receipt text-primary me-2"></i>Riwayat Transaksi</h6>
            </div>
            <div class="col-md-8 text-end">
                <form id="filter-form" class="d-flex justify-content-end gap-2">
                    <input type="date" name="date" class="form-control form-control-sm w-auto">
                    <select name="order_status" class="form-select form-select-sm w-auto">
                        <option value="">Semua Status Dapur</option>
                        <option value="waiting">Menunggu</option>
                        <option value="processing">Diproses</option>
                        <option value="ready">Siap</option>
                        <option value="completed">Selesai</option>
                        <option value="cancelled">Dibatalkan</option>
                    </select>
                    <div class="input-group input-group-sm w-auto">
                        <input type="text" name="search" class="form-control" placeholder="Cari kode/nama...">
                        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Bagian Toolbar Bulk Actions (Sembunyikan default) -->
    <div class="bg-light p-2 border-bottom d-none align-items-center justify-content-between" id="bulk-toolbar">
        <div>
            <span class="badge bg-primary rounded-pill me-2" id="selected-count">0</span>
            <span class="small fw-medium">Transaksi terpilih</span>
        </div>
        <button type="button" class="btn btn-sm btn-danger" id="btn-bulk-delete"><i class="bi bi-trash me-1"></i>Hapus Terpilih</button>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 40px;">
                            <input class="form-check-input" type="checkbox" id="check-all">
                        </th>
                        <th>Kode Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Waktu</th>
                        <th>Total</th>
                        <th>Status Pembayaran</th>
                        <th>Status Dapur</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="transactions-tbody">
                    <tr><td colspan="8" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white pt-4 pb-3 border-top-0 d-flex justify-content-end" id="pagination-container">
    </div>
</div>

<!-- Modal Struk -->
<div class="modal fade" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0 pb-4 px-4" id="receipt-content">
                <!-- Struk dimuat via JS -->
            </div>
            <div class="modal-footer border-0 pt-0 d-flex justify-content-center pb-4">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printReceipt()"><i class="bi bi-printer me-1"></i>Cetak Struk</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #receiptModal .modal-content, #receiptModal .modal-content * { visibility: visible; }
        #receiptModal .modal-content { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; border: none !important; }
        .modal-footer { display: none !important; }
        .btn-close { display: none !important; }
    }
</style>
@endpush

@push('scripts')
<script src="/js/admin/transactions.js"></script>
@endpush