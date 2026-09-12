@extends('layouts.admin')

@section('title', 'Monitor Dapur (Kasir)')

@section('content')
<div class="row g-4">
    @forelse($activeOrders as $order)
    <div class="col-md-6 col-lg-4">
        <!-- Order Card -->
        <div class="card border-0 shadow-sm h-100 overflow-hidden {{ $order->order_status == 'ready' ? 'border-success border border-2' : '' }}">
            
            <!-- Header Status Warna -->
            <div class="card-header text-white border-bottom-0 py-3 
                {{ $order->order_status == 'processing' ? 'bg-primary' : ($order->order_status == 'ready' ? 'bg-success' : 'bg-secondary') }}">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold font-monospace fs-5">{{ $order->order_code }}</span>
                    <span class="badge bg-white text-dark rounded-pill shadow-sm">
                        {{ $order->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>

            <!-- Body Info -->
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <div>
                        <small class="text-muted d-block text-uppercase">Pemesan</small>
                        <span class="fw-bold fs-5">{{ $order->customer_name }}</span>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block text-uppercase">Meja</small>
                        <span class="fw-bold fs-5 text-brand" style="color: #c97d20;">
                            {{ $order->table_number ?: 'TAKE AWAY' }}
                        </span>
                    </div>
                </div>

                <!-- Daftar Item -->
                <ul class="list-group list-group-flush mb-3">
                    @foreach($order->details as $item)
                    <li class="list-group-item px-0 py-2 border-light">
                        <div class="d-flex align-items-start">
                            <span class="badge bg-dark rounded-pill me-2 fs-6 mt-1">{{ $item->quantity }}x</span>
                            <div>
                                <span class="fw-bold">{{ $item->product_name }}</span>
                                @if($item->notes)
                                    <br><small class="text-danger fw-medium fst-italic"><i class="bi bi-exclamation-circle me-1"></i>{{ $item->notes }}</small>
                                @endif
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>

                @if($order->notes)
                <div class="alert alert-warning py-2 mb-0 mt-3 small">
                    <strong>Catatan:</strong> {{ $order->notes }}
                </div>
                @endif
            </div>

            <!-- Footer Action Buttons -->
            <div class="card-footer bg-white border-top pb-3 pt-3">
                <form action="{{ route('admin.kitchen.update', $order->id) }}" method="POST" class="d-flex gap-2">
                    @csrf
                    @method('PATCH')
                    
                    @if($order->order_status == 'waiting')
                        <button type="submit" name="order_status" value="processing" class="btn btn-primary w-100 fw-bold shadow-sm py-2">
                            <i class="bi bi-play-circle me-1"></i> Mulai Proses
                        </button>
                    @elseif($order->order_status == 'processing')
                        <button type="submit" name="order_status" value="ready" class="btn btn-success w-100 fw-bold shadow-sm py-2">
                            <i class="bi bi-check2-circle me-1"></i> Tandai Siap Diambil
                        </button>
                    @elseif($order->order_status == 'ready')
                        <button type="submit" name="order_status" value="completed" class="btn btn-outline-secondary w-100 fw-bold py-2">
                            <i class="bi bi-box-arrow-right me-1"></i> Selesaikan Pesanan
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="text-center py-5">
            <div class="display-1 text-muted opacity-25 mb-3"><i class="bi bi-cup-hot"></i></div>
            <h4 class="text-muted fw-bold">Dapur Sedang Santai</h4>
            <p class="text-muted">Tidak ada pesanan aktif yang menunggu diproses.</p>
        </div>
    </div>
    @endforelse
</div>

<!-- Auto Refresh Page every 15 seconds to fetch new orders -->
<script>
    setTimeout(function(){
        window.location.reload();
    }, 15000);
</script>
@endsection
