@extends('layouts.admin')

@section('title', 'Bahan Baku')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-4 pb-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-basket2 text-primary me-2"></i>Daftar Bahan Baku (Inventory)</h6>
        <a href="{{ route('admin.ingredients.create') }}" class="btn btn-sm btn-primary shadow-sm"><i class="bi bi-plus-lg me-1"></i> Tambah Bahan</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Bahan Baku</th>
                        <th>Satuan (Unit)</th>
                        <th>Sisa Stok Saat Ini</th>
                        <th>Batas Stok Kritis (Minimum)</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ingredients as $idx => $ing)
                    @php
                        $isCritical = $ing->stock_quantity <= $ing->minimum_stock;
                    @endphp
                    <tr class="{{ $isCritical ? 'table-danger' : '' }}">
                        <td class="ps-4 text-muted">{{ $idx + 1 }}</td>
                        <td class="fw-bold">{{ $ing->name }}
                            @if($isCritical)
                                <span class="badge bg-danger ms-2" style="font-size: 0.65rem;">Kritis!</span>
                            @endif
                        </td>
                        <td><span class="badge bg-secondary rounded-pill px-3">{{ $ing->unit }}</span></td>
                        <td class="fw-bold {{ $isCritical ? 'text-danger' : 'text-success' }}">
                            {{ floatval($ing->stock_quantity) }} {{ $ing->unit }}
                        </td>
                        <td class="text-muted">{{ floatval($ing->minimum_stock) }} {{ $ing->unit }}</td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('admin.ingredients.edit', $ing->id) }}" class="btn btn-sm btn-success" title="Edit / Restock"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.ingredients.destroy', $ing->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan baku ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada bahan baku tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($ingredients->hasPages())
    <div class="card-footer bg-white pt-4 pb-3 border-top-0">
        {{ $ingredients->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
