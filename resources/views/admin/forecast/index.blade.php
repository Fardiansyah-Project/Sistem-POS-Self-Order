@extends('layouts.admin')

@section('title', 'Peramalan Bahan Baku (WMA)')

@section('content')
<div class="row">
    <!-- Kontrol Panel -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white pt-3 pb-2">
                <h6 class="fw-bold"><i class="bi bi-sliders me-2 text-primary"></i>Kontrol Peramalan</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.forecast.run') }}" method="POST" class="mb-4">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small">Bobot WMA (Pisahkan koma)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-percent"></i></span>
                            <input type="text" name="weights" class="form-control" value="1,2,3" required placeholder="Contoh: 1,2,3">
                        </div>
                        <div class="form-text" style="font-size: 0.75rem;">Urutan bobot dari periode terlama -> terbaru.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 text-white fw-medium shadow-sm">
                        <i class="bi bi-play-fill me-1"></i> Jalankan Prediksi (Semua Bahan)
                    </button>
                </form>

                <hr>

                <form method="GET" action="{{ route('admin.forecast.index') }}">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Pilih Bahan Baku (Grafik)</label>
                        <select name="ingredient_id" class="form-select shadow-sm" onchange="this.form.submit()">
                            @foreach($ingredients as $ing)
                            <option value="{{ $ing->id }}" {{ $selectedIngredientId == $ing->id ? 'selected' : '' }}>
                                {{ $ing->name }} ({{ $ing->unit }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Hasil -->
        @if($chartData)
        <div class="card border-0 shadow-sm border-top border-primary border-3">
            <div class="card-body">
                <h6 class="text-muted small text-uppercase mb-3">Hasil Prediksi Bulan Depan</h6>
                <div class="display-5 fw-bold text-primary mb-1">
                    {{ number_format($chartData['next_forecast'], 1) }}
                </div>
                <div class="text-muted small mb-3">Satuan unit dibutuhkan</div>

                <div class="d-flex justify-content-between border-top pt-3 mt-3">
                    <span class="text-muted small">Bobot yg digunakan:</span>
                    <span class="fw-bold">[{{ $chartData['weights'] }}]</span>
                </div>
                <div class="d-flex justify-content-between pt-2">
                    <span class="text-muted small">Akurasi (MAE):</span>
                    <span class="fw-bold text-success">{{ number_format($chartData['mae'], 2) }} error margin</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Grafik Visualisasi -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white pt-3 pb-2 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Grafik Aktual vs Peramalan</h6>
                <span class="badge bg-light text-dark border"><i class="bi bi-info-circle me-1"></i>Garis putus-putus = WMA</span>
            </div>
            <div class="card-body">
                @if($chartData)
                <canvas id="wmaChart" height="150"></canvas>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-graph-down display-1 opacity-25"></i>
                    <p class="mt-3">Belum ada data peramalan untuk bahan baku ini.</p>
                    <p class="small">Silakan klik "Jalankan Peramalan" terlebih dahulu.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($chartData)
<script>
    const ctx = document.getElementById('wmaChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                    label: 'Penggunaan Aktual',
                    data: {!! json_encode($chartData['actuals']) !!},
                    borderColor: '#4361ee',
                    backgroundColor: '#4361ee',
                    borderWidth: 2,
                    tension: 0.1,
                    pointRadius: 4,
                    pointHoverRadius: 6
                },
                {
                    label: 'Hasil Peramalan (WMA)',
                    data: {!! json_encode($chartData['forecasts']) !!},
                    borderColor: '#c97d20',
                    backgroundColor: '#c97d20',
                    borderWidth: 3,
                    borderDash: [5, 5], // Garis putus-putus
                    tension: 0.1,
                    pointStyle: 'rectRot',
                    pointRadius: 6,
                    pointHoverRadius: 8
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) label += ': ';
                            if (context.parsed.y !== null) {
                                label += parseFloat(context.parsed.y).toFixed(2);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah Penggunaan'
                    }
                }
            }
        }
    });
</script>
@endif
@endpush