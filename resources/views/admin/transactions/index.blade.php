@extends('layouts.admin')

@section('title', 'Data Transaksi')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-4 pb-3 d-flex justify-content-between align-items-center border-bottom-0">
        <h6 class="fw-bold mb-0 text-uppercase"><i class="bi bi-receipt me-2 text-primary"></i>Riwayat Transaksi</h6>
        
        <!-- Filter Form -->
        <form action="{{ route('admin.transactions.index') }}" method="GET" class="d-flex align-items-center">
            <select name="payment_status" class="form-select form-select-sm shadow-sm me-2" onchange="this.form.submit()">
                <option value="">Semua Status Pembayaran</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Sudah Lunas (Paid)</option>
                <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Gagal / Batal</option>
            </select>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Kode Order</th>
                        <th>Waktu</th>
                        <th>Pemesan</th>
                        <th>Total Item</th>
                        <th>Total Bayar</th>
                        <th>Pembayaran</th>
                        <th>Status Dapur</th>
                        <th class="pe-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    <tr>
                        <td class="ps-4 font-monospace fw-bold text-primary">{{ $trx->order_code }}</td>
                        <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="fw-medium">{{ $trx->customer_name }}</span><br>
                            <small class="text-muted">Meja: {{ $trx->table_number ?: 'Take-away' }}</small>
                        </td>
                        <td>{{ $trx->details->sum('quantity') }} item</td>
                        <td class="fw-bold text-success">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</td>
                        <td>
                            @if($trx->payment_status == 'paid')
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Paid</span>
                            @elseif($trx->payment_status == 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3">Pending</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">{{ ucfirst($trx->payment_status) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary rounded-pill px-3">{{ ucfirst($trx->order_status) }}</span>
                        </td>
                        <td class="pe-4 text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modal-{{ $trx->id }}">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt display-4 opacity-25 d-block mb-3"></i>
                            Tidak ada data transaksi yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach($transactions as $trx)
    <div class="modal fade" id="modal-{{ $trx->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Detail Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="font-monospace fs-4 fw-bold text-primary">{{ $trx->order_code }}</div>
                        <div class="text-muted small">{{ $trx->created_at->format('d F Y, H:i:s') }}</div>
                    </div>
                    <div class="bg-light p-3 rounded-3 mb-4">
                        <div class="row text-sm">
                            <div class="col-6 text-muted">Pemesan</div>
                            <div class="col-6 fw-bold text-end">{{ $trx->customer_name }}</div>
                            <div class="col-6 text-muted">Meja</div>
                            <div class="col-6 fw-bold text-end">{{ $trx->table_number ?: 'Take Away' }}</div>
                            <div class="col-6 text-muted">Tipe Bayar</div>
                            <div class="col-6 fw-bold text-end text-uppercase">{{ $trx->payment_type ?: '-' }}</div>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Item Pesanan</h6>
                    <div class="mb-3">
                        @foreach($trx->details as $item)
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light">
                            <div>
                                <span class="fw-bold me-2">{{ $item->quantity }}x</span>{{ $item->product_name }}
                                @if($item->notes)
                                <div class="text-muted small ms-4 fst-italic"><i class="bi bi-arrow-return-right me-1"></i>{{ $item->notes }}</div>
                                @endif
                            </div>
                            <div class="fw-medium text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                        @endforeach
                    </div>
                    @if($trx->notes)
                    <div class="alert alert-warning py-2 small mb-3"><i class="bi bi-card-text me-2"></i><strong>Catatan:</strong> {{ $trx->notes }}</div>
                    @endif
                    <div class="d-flex justify-content-between fs-5 border-top pt-3">
                        <span class="fw-bold text-muted">Total</span>
                        <span class="fw-bold" style="color: #c97d20;">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</span>
                    </div>

                    <div id="receipt-{{ $trx->id }}" class="receipt-print" aria-hidden="true">
                        <div class="receipt-header">
                            <h1>Koriro Coffee</h1>
                            <div>Jl. Soekarno Hatta, Tondo, Kota Palu, Sulawesi Tengah</div>
                            <div class='mb-2'>Telp: 0813-9609-332</div>
                            <div>Struk Pembayaran</div>
                        </div>
                        <div class="receipt-divider">--------------------------------</div>
                        <div class="receipt-meta">
                            <div>No: {{ $trx->order_code }}</div>
                            <div>{{ $trx->created_at->format('d/m/Y H:i') }}</div>
                            <div>Pemesan: {{ $trx->customer_name }}</div>
                            <div>Meja: {{ $trx->table_number ?: 'Take Away' }}</div>
                        </div>
                        <div class="receipt-divider">--------------------------------</div>
                        @foreach($trx->details as $item)
                        <div class="receipt-item">
                            <div>{{ $item->quantity }}x {{ $item->product_name }}</div>
                            <div>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                        @endforeach
                        <div class="receipt-divider">--------------------------------</div>
                        <div class="receipt-total"><span>Subtotal</span><span>Rp {{ number_format($trx->subtotal, 0, ',', '.') }}</span></div>
                        @if($trx->tax_amount > 0)
                        <div class="receipt-total"><span>Pajak</span><span>Rp {{ number_format($trx->tax_amount, 0, ',', '.') }}</span></div>
                        @endif
                        <div class="receipt-total receipt-grand-total"><span>TOTAL</span><span>Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</span></div>
                        <div class="receipt-meta receipt-payment">
                            <div>Pembayaran: {{ strtoupper($trx->payment_type ?: '-') }}</div>
                            <div>Status: {{ strtoupper($trx->payment_status) }}</div>
                        </div>
                        <div class="receipt-footer">Terima kasih telah berkunjung</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-primary" onclick="printReceipt('receipt-{{ $trx->id }}')">
                        <i class="bi bi-printer me-1"></i> Cetak Struk
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    
    @if($transactions->hasPages())
    <div class="card-footer bg-white pt-4 pb-3 border-top-0">
        {{ $transactions->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .receipt-print {
        display: none;
    }

    @media print {
        @page {
            size: 80mm auto;
            margin: 4mm;
        }

        body.printing-receipt * {
            visibility: hidden !important;
        }

        body.printing-receipt .receipt-print.receipt-active,
        body.printing-receipt .receipt-print.receipt-active * {
            visibility: visible !important;
        }

        body.printing-receipt .receipt-print.receipt-active {
            display: block !important;
            position: absolute;
            top: 0;
            left: 0;
            width: 72mm;
            color: #000;
            background: #fff;
            font-family: "Courier New", monospace;
            font-size: 11px;
            line-height: 1.35;
        }

        .receipt-header {
            text-align: center;
        }

        .receipt-header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .receipt-divider {
            overflow: hidden;
            white-space: nowrap;
            margin: 5px 0;
        }

        .receipt-meta {
            margin: 4px 0;
        }

        .receipt-item,
        .receipt-total {
            display: flex;
            justify-content: space-between;
            gap: 8px;
        }

        .receipt-item > div:first-child {
            max-width: 48mm;
            overflow-wrap: anywhere;
        }

        .receipt-grand-total {
            margin-top: 4px;
            font-size: 13px;
            font-weight: 700;
        }

        .receipt-payment {
            margin-top: 8px;
        }

        .receipt-footer {
            margin-top: 14px;
            text-align: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function printReceipt(receiptId) {
        const receipt = document.getElementById(receiptId);

        if (!receipt) return;

        receipt.classList.add('receipt-active');
        document.body.classList.add('printing-receipt');

        window.setTimeout(() => {
            window.print();
        }, 100);
    }

    window.addEventListener('afterprint', () => {
        document.body.classList.remove('printing-receipt');
        document.querySelectorAll('.receipt-active').forEach((receipt) => {
            receipt.classList.remove('receipt-active');
        });
    });
</script>
@endpush
