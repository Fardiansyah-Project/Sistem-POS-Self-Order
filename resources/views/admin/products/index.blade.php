@extends('layouts.admin')

@section('title', 'Produk Menu')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-4 pb-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Daftar Produk / Menu</h6>
        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary shadow-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Menu</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Info Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $prod)
                    <tr>
                        <td class="ps-4 d-flex align-items-center">
                            @if($prod->image)
                                <img src="{{ $prod->image_url }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                    <i class="bi bi-image"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $prod->name }}</h6>
                                <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">{{ $prod->description }}</small>
                            </div>
                        </td>
                        <td><span class="badge bg-secondary rounded-pill">{{ $prod->category->name ?? '-' }}</span></td>
                        <td class="fw-medium text-brand">Rp {{ number_format($prod->price, 0, ',', '.') }}</td>
                        <td>
                            @if($prod->is_available)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Tersedia</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">Habis / Kosong</span>
                            @endif
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <!-- Tombol Atur Resep WMA -->
                                <a href="{{ route('admin.recipes.index', $prod->id) }}" class="btn btn-sm btn-outline-info" title="Atur Resep (Bahan Baku)"><i class="bi bi-list-nested"></i></a>
                                
                                <a href="{{ route('admin.products.edit', $prod->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada produk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($products->hasPages())
    <div class="card-footer bg-white pt-4 pb-3 border-top-0">
        {{ $products->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
