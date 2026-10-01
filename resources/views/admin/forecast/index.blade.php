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
                <form id="form-run-forecast" class="mb-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small">Bobot WMA (Pisahkan koma)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-percent"></i></span>
                            <input type="text" name="weights" id="wma-weights" class="form-control" value="1,2,3" required placeholder="Contoh: 1,2,3">
                        </div>
                        <div class="form-text" style="font-size: 0.75rem;">Urutan bobot dari periode terlama -> terbaru.</div>
                    </div>
                    <button type="submit" id="btn-run" class="btn btn-primary w-100 text-white fw-medium shadow-sm">
                        <i class="bi bi-play-fill me-1"></i> Jalankan Prediksi (Semua Bahan)
                    </button>
                </form>

                <hr>

                <div class="mb-3">
                    <label class="form-label text-muted small">Pilih Bahan Baku (Grafik)</label>
                    <select id="ingredient-select" class="form-select shadow-sm">
                        <option value="">Memuat bahan baku...</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Info Hasil -->
        <div id="forecast-result-container" class="d-none">
            <div class="card border-0 shadow-sm border-top border-primary border-3">
                <div class="card-body">
                    <h6 class="text-muted small text-uppercase mb-3">Hasil Prediksi Bulan Depan</h6>
                    <div class="display-5 fw-bold text-primary mb-1" id="next-forecast-val">0</div>
                    <div class="text-muted small mb-3">Satuan unit dibutuhkan</div>

                    <div class="d-flex justify-content-between border-top pt-3 mt-3">
                        <span class="text-muted small">Bobot yg digunakan:</span>
                        <span class="fw-bold" id="used-weights">[1, 2, 3]</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2">
                        <span class="text-muted small">Akurasi (MAE):</span>
                        <span class="fw-bold text-success"><span id="mae-val">0</span> error margin</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Visualisasi -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white pt-3 pb-2 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-bar-chart-fill me-2 text-primary"></i>Grafik Aktual vs Peramalan</h6>
                <span class="badge bg-light text-dark border"><i class="bi bi-info-circle me-1"></i>Garis putus-putus = WMA</span>
            </div>
            <div class="card-body">
                <div id="chart-container" class="d-none">
                    <canvas id="wmaChart" height="150"></canvas>
                </div>
                
                <div id="chart-empty-state" class="text-center py-5 text-muted">
                    <i class="bi bi-graph-down display-1 opacity-25"></i>
                    <p class="mt-3">Belum ada data peramalan untuk bahan baku ini.</p>
                    <p class="small">Silakan klik "Jalankan Peramalan" terlebih dahulu.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/admin/forecast.js"></script>
@endpush