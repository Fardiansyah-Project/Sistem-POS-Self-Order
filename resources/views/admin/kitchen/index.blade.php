@extends('layouts.admin')

@section('title', 'Monitor Dapur (Kasir)')

@section('content')
<div class="row g-4" id="kitchen-container">
    <div class="col-12 text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="text-muted mt-2">Memuat pesanan dapur...</p>
    </div>
</div>
@endsection

@push('scripts')
<script src="/js/admin/kitchen.js"></script>
@endpush
