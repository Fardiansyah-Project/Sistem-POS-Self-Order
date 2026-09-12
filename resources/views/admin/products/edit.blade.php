@extends('layouts.admin')

@section('title', 'Edit Produk Menu')

@section('content')
<div class="card border-0 shadow-sm max-w-3xl mx-auto">
    <div class="card-header bg-white pt-4 pb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Menu: {{ $product->name }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label text-muted small fw-medium">Nama Produk / Menu</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label text-muted small fw-medium">Kategori</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small fw-medium">Deskripsi Menu</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $product->description) }}</textarea>
                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small fw-medium">Harga Jual (Rp)</label>
                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', floatval($product->price)) }}" required>
                    @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted small fw-medium">Status Ketersediaan</label>
                    <select name="is_available" class="form-select @error('is_available') is-invalid @enderror" required>
                        <option value="1" {{ old('is_available', $product->is_available) == '1' ? 'selected' : '' }}>Tersedia (Bisa Dipesan)</option>
                        <option value="0" {{ old('is_available', $product->is_available) == '0' ? 'selected' : '' }}>Habis (Sembunyikan dari Menu)</option>
                    </select>
                    @error('is_available') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mb-4">
                    <label class="form-label text-muted small fw-medium">Gambar Produk (Opsional)</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp">
                    @if($product->image)
                        <div class="form-text small">Gambar saat ini: {{ basename($product->image) }}. Pilih file baru jika ingin menggantinya.</div>
                    @else
                        <div class="form-text small">Format JPG, JPEG, PNG, atau WEBP. Maksimal 2 MB.</div>
                    @endif
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-4">
                    <label class="form-label text-muted small fw-medium">Urutan Tampil (Sort Order)</label>
                    <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $product->sort_order) }}" required>
                    @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex gap-2 border-top pt-4">
                <a href="{{ route('admin.products.index') }}" class="btn btn-light border">Batal</a>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Perbarui Produk</button>
            </div>
        </form>
    </div>
</div>
@endsection
