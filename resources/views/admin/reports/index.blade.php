@extends('layouts.admin')

@section('title', 'Laporan Berkala')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Ekspor Laporan Penjualan</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.reports.sales') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="start_date_sales" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date_sales" name="start_date" required value="{{ date('Y-m-01') }}">
            </div>
            <div class="col-md-3">
                <label for="end_date_sales" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="end_date_sales" name="end_date" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-6 d-flex gap-2">
                <button type="submit" name="format" value="pdf" class="btn btn-danger text-white flex-grow-1">
                    <i class="bi bi-file-earmark-pdf"></i> Ekspor PDF
                </button>
                <button type="submit" name="format" value="excel" class="btn btn-success text-white flex-grow-1">
                    <i class="bi bi-file-earmark-excel"></i> Ekspor Excel
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Ekspor Laporan Peramalan (WMA)</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.reports.forecast') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="start_date_forecast" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date_forecast" name="start_date" required value="{{ date('Y-m-01') }}">
            </div>
            <div class="col-md-3">
                <label for="end_date_forecast" class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" id="end_date_forecast" name="end_date" required value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-6 d-flex gap-2">
                <button type="submit" name="format" value="pdf" class="btn btn-danger text-white flex-grow-1">
                    <i class="bi bi-file-earmark-pdf"></i> Ekspor PDF
                </button>
                <button type="submit" name="format" value="excel" class="btn btn-success text-white flex-grow-1">
                    <i class="bi bi-file-earmark-excel"></i> Ekspor Excel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
