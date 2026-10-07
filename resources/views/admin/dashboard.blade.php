@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <button type="button" class="btn btn-danger" id="toggle-order-status" data-open="1">
        <i class="bi bi-lock-fill me-1"></i> Tutup Order Customer
    </button>
</div>
<div class="row g-4 mb-4">
    <!-- Card Pendapatan -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                    <i class="bi bi-currency-dollar fs-3"></i>
                </div>
                <div>
                    <h6 class="card-title text-muted mb-1">Pendapatan Hari Ini</h6>
                    <h3 class="fw-bold mb-0" id="stat-revenue">Memuat...</h3>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card Pesanan -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                    <i class="bi bi-bag-check fs-3"></i>
                </div>
                <div>
                    <h6 class="card-title text-muted mb-1">Total Pesanan (Hari Ini)</h6>
                    <h3 class="fw-bold mb-0"><span id="stat-orders">...</span> <span class="fs-6 text-muted fw-normal">pesanan</span></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Stok Kritis -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3 me-3">
                    <i class="bi bi-exclamation-triangle fs-3"></i>
                </div>
                <div>
                    <h6 class="card-title text-muted mb-1">Stok Bahan Kritis</h6>
                    <h3 class="fw-bold mb-0"><span id="stat-critical-stock">...</span> <span class="fs-6 text-muted fw-normal">item</span></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart Penjualan -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h6 class="fw-bold"><i class="bi bi-graph-up me-2 text-primary"></i>Grafik Penjualan 7 Hari Terakhir</h6>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Produk & Peringatan Stok -->
    <div class="col-md-4">
        <!-- Top Products -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h6 class="fw-bold"><i class="bi bi-star-fill me-2 text-warning"></i>Produk Terlaris Bulan Ini</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush" id="top-products-list">
                    <li class="list-group-item text-center py-4 text-muted">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div> Memuat data...
                    </li>
                </ul>
            </div>
        </div>

        <!-- Stok Kritis List -->
        <div class="card border-0 shadow-sm border-top border-danger border-3">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h6 class="fw-bold text-danger"><i class="bi bi-bell-fill me-2"></i>Peringatan Stok Kritis!</h6>
            </div>
            <div class="card-body" id="critical-stock-container">
                <div class="text-center py-3 text-muted">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div> Memuat data...
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/admin/dashboard.js"></script>
@endpush
