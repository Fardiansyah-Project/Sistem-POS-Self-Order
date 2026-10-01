@extends('layouts.admin')

@section('title', 'Kasir POS')

@section('content')
<div class="row g-3">
    <!-- Left Panel: Products -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white pt-3 pb-2 border-bottom-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-uppercase"><i class="bi bi-box-seam me-2 text-primary"></i>Katalog Menu</h6>
                <!-- Category Filter -->
                <select id="category-filter" class="form-select form-select-sm shadow-sm" style="width: 200px;">
                    <option value="">Semua Kategori</option>
                </select>
            </div>
            <div class="card-body bg-light rounded-bottom p-3" style="max-height: calc(100vh - 180px); overflow-y: auto;">
                <div class="row g-3" id="products-container">
                    <!-- Products loaded via JS -->
                    <div class="col-12 text-center py-5">
                        <div class="spinner-border text-primary"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Cart & Checkout -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100 d-flex flex-column">
            <div class="card-header bg-white pt-3 pb-2 border-bottom-0">
                <h6 class="fw-bold mb-0 text-uppercase"><i class="bi bi-cart3 me-2 text-primary"></i>Keranjang Pesanan</h6>
            </div>

            <div class="card-body p-0 flex-grow-1 d-flex flex-column" style="max-height: calc(100vh - 450px); overflow-y: auto;">
                <div id="cart-items-container" class="w-100"></div>

                <div class="text-center py-5 text-muted flex-grow-1 mt-4" id="empty-cart-msg">
                    <i class="bi bi-cart-x display-4 d-block opacity-25 mb-3"></i>
                    Keranjang masih kosong
                </div>
            </div>

            <div class="card-footer bg-white p-3 border-top">
                <!-- Order Summary -->
                <div class="d-flex justify-content-between mb-1 small text-muted">
                    <span>Subtotal</span>
                    <span id="summary-subtotal">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small text-muted">
                    <span>PPN (11%)</span>
                    <span id="summary-tax">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between mb-3 fw-bold fs-5 text-brand">
                    <span>Total Tagihan</span>
                    <span id="summary-total">Rp 0</span>
                </div>

                <!-- Checkout Form -->
                <form id="checkout-form">
                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm" id="customer_name" placeholder="Nama Pelanggan *" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm" id="table_number" placeholder="Nomor Meja (Opsional)">
                    </div>
                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm" id="order_notes" placeholder="Catatan Transaksi (Opsional)">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <select id="payment_type" class="form-select form-select-sm" required>
                                <option value="cash">Tunai (Cash)</option>
                                <option value="qris">QRIS / e-Wallet</option>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="edc">Kartu Debit/Kredit</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control form-control-sm" id="amount_paid" placeholder="Uang Diterima *" required min="0">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-3 small fw-bold" id="kembalian-container" style="display: none !important;">
                        <span>Kembalian:</span>
                        <span id="kembalian-text" class="text-success">Rp 0</span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" id="btn-submit">
                        <i class="bi bi-check2-circle me-1"></i> Proses Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="resetPOS()"></button>
            </div>
            <div class="modal-body pt-0 pb-4 px-4" id="receipt-content">
                <!-- Struk dimuat via JS -->
            </div>
            <div class="modal-footer border-0 pt-0 d-flex justify-content-center pb-4">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Cetak Struk</button>
                <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" onclick="resetPOS()">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }
    .cart-item-qty {
        width: 40px;
        text-align: center;
    }

    /* Print styles */
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
<script src="/js/admin/pos.js"></script>
@endpush