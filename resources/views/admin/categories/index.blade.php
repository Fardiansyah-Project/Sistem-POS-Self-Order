@extends('layouts.admin')

@section('title', 'Kategori Menu')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-4 pb-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-tags text-primary me-2"></i>Daftar Kategori</h6>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary shadow-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Kategori</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Icon</th>
                        <th>Nama Kategori</th>
                        <th>Status</th>
                        <th>Total Produk</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $idx => $cat)
                    <tr>
                        <td class="ps-4 text-muted">{{ $idx + 1 }}</td>
                        <td class="fs-4">{{ $cat->icon }}</td>
                        <td class="fw-bold">{{ $cat->name }}</td>
                        <td>
                            @if($cat->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Aktif</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3">Non-aktif</span>
                            @endif
                        </td>
                        <td>{{ $cat->products_count }} item</td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.categories.edit', $cat->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada kategori.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
