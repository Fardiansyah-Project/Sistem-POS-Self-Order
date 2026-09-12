@extends('layouts.admin')

@section('title', 'Edit Bahan Baku')

@section('content')
<div class="card border-0 shadow-sm max-w-2xl mx-auto">
    <div class="card-header bg-white pt-4 pb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Bahan Baku: {{ $ingredient->name }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.ingredients.update', $ingredient->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label text-muted small fw-medium">Nama Bahan Baku</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $ingredient->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small fw-medium">Satuan (Unit)</label>
                <input type="text" name="unit" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit', $ingredient->unit) }}" required>
                @error('unit') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small fw-medium">Sisa Stok Saat Ini</label>
                    <input type="number" step="0.01" name="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity', floatval($ingredient->stock_quantity)) }}" required>
                    @error('stock_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label text-muted small fw-medium">Batas Stok Kritis (Minimum)</label>
                    <input type="number" step="0.01" name="minimum_stock" class="form-control @error('minimum_stock') is-invalid @enderror" value="{{ old('minimum_stock', floatval($ingredient->minimum_stock)) }}" required>
                    @error('minimum_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 border-top pt-4">
                <a href="{{ route('admin.ingredients.index') }}" class="btn btn-light border">Batal</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Perbarui Data</button>
            </div>
        </form>
    </div>
</div>
@endsection
