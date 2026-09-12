@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')
<div class="card border-0 shadow-sm max-w-2xl mx-auto">
    <div class="card-header bg-white pt-4 pb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-plus-circle text-primary me-2"></i>Tambah Kategori Menu</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label text-muted small fw-medium">Nama Kategori</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="Contoh: Espresso Based">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small fw-medium">Ikon (Emoji atau text singkat)</label>
                <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon') }}" placeholder="Contoh: ☕">
                @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small fw-medium">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 0) }}" required>
                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label text-muted small fw-medium">Status Aktif</label>
                    <select name="is_active" class="form-select @error('is_active') is-invalid @enderror" required>
                        <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Non-aktif (Sembunyikan)</option>
                    </select>
                    @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 border-top pt-4">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light border">Batal</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
