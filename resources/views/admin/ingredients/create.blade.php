@extends('layouts.admin')

@section('title', 'Tambah Bahan Baku')

@section('content')
<div class="card border-0 shadow-sm max-w-2xl mx-auto">
    <div class="card-header bg-white pt-4 pb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Bahan Baku Baru</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.ingredients.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label text-muted small fw-medium">Nama Bahan Baku</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Contoh: Kopi Arabica">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small fw-medium">Satuan (Unit)</label>
                <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit') }}" required placeholder="Contoh: Gram, Ml, Pcs">
                @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small fw-medium">Stok Awal</label>
                    <input type="number" step="0.01" name="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity', '0') }}" required>
                    @error('stock_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label text-muted small fw-medium">Batas Stok Kritis (Minimum)</label>
                    <input type="number" step="0.01" name="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" value="{{ old('minimum_stock', '0') }}" required>
                    @error('minimum_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 border-top pt-4">
                <a href="{{ route('admin.ingredients.index') }}" class="btn btn-light border">Batal</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
