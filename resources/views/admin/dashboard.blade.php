@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
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
                    <h3 class="fw-bold mb-0">Rp {{ number_format($revenueToday, 0, ',', '.') }}</h3>
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
                    <h3 class="fw-bold mb-0">{{ $ordersToday }} <span class="fs-6 text-muted fw-normal">pesanan</span></h3>
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
                    <h3 class="fw-bold mb-0">{{ $criticalIngredients->count() }} <span class="fs-6 text-muted fw-normal">item</span></h3>
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
                <ul class="list-group list-group-flush">
                    @forelse($topProducts as $idx => $prod)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <span class="badge bg-secondary rounded-circle me-2">{{ $idx + 1 }}</span>
                            {{ $prod->product_name }}
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $prod->total_sold }} qty</span>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-3">Belum ada penjualan bulan ini.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Stok Kritis List -->
        <div class="card border-0 shadow-sm border-top border-danger border-3">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h6 class="fw-bold text-danger"><i class="bi bi-bell-fill me-2"></i>Peringatan Stok Kritis!</h6>
            </div>
            <div class="card-body">
                @if($criticalIngredients->isEmpty())
                    <p class="text-success mb-0 small"><i class="bi bi-check-circle me-1"></i> Semua stok bahan baku aman.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless small mb-0">
                            <tbody>
                                @foreach($criticalIngredients as $ing)
                                <tr>
                                    <td>{{ $ing->name }}</td>
                                    <td class="text-end text-danger fw-bold">{{ floatval($ing->stock_quantity) }} {{ $ing->unit }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <a href="/admin/ingredients" class="btn btn-sm btn-outline-danger w-100 mt-3">Restock Sekarang</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesData['labels']) !!},
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: {!! json_encode($salesData['data']) !!},
                borderColor: '#c97d20',
                backgroundColor: 'rgba(201, 125, 32, 0.1)',
                borderWidth: 3,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#c97d20'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value/1000) + 'k';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
