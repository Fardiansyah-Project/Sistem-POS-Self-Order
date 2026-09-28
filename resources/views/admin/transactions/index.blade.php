@extends('layouts.admin')

@section('title', 'Data Transaksi')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-4 pb-3 d-flex justify-content-between align-items-center border-bottom-0">
        <h6 class="fw-bold mb-0 text-uppercase"><i class="bi bi-receipt me-2 text-primary"></i>Riwayat Transaksi</h6>

        <div class="d-flex align-items-center gap-2">
            <!-- Bulk Action Toolbar (muncul saat ada checkbox tercentang) -->
            <div id="bulk-toolbar" class="d-none">
                <form id="bulk-delete-form" action="{{ route('admin.transactions.bulkDestroy') }}" method="POST"
                    class="d-inline">
                    @csrf
                    @method('DELETE')
                    <div id="bulk-ids-container"></div>
                    <button type="button" id="btn-bulk-delete" class="btn btn-sm btn-danger shadow-sm">
                        <i class="bi bi-trash me-1"></i> Hapus Terpilih (<span id="selected-count">0</span>)
                    </button>
                </form>
                <button type="button" id="btn-deselect-all" class="btn btn-sm btn-outline-secondary shadow-sm ms-1">
                    <i class="bi bi-x-lg me-1"></i> Batal Pilih
                </button>
            </div>

            <!-- Filter Form -->
            <form action="{{ route('admin.transactions.index') }}" method="GET" class="d-flex align-items-center">
                <select name="payment_status" class="form-select form-select-sm shadow-sm me-2"
                    onchange="this.form.submit()">
                    <option value="">Semua Status Pembayaran</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Sudah Lunas
                        (Paid)</option>
                    <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Menunggu
                        (Pending)</option>
                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Gagal / Batal
                    </option>
                </select>
            </form>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="width: 50px;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all" title="Pilih Semua">
                            </div>
                        </th>
                        <th>Kode Order</th>
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
                        <td class="ps-4">
                            <div class="form-check">
                                <input class="form-check-input row-checkbox" type="checkbox"
                                    value="{{ $trx->id }}" data-order-code="{{ $trx->order_code }}">
                            </div>
                        </td>
                        <td class="font-monospace fw-bold text-primary">{{ $trx->order_code }}</td>
                        <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <span class="fw-medium">{{ $trx->customer_name }}</span><br>
                            <small class="text-muted">Meja: {{ $trx->table_number ?: 'Take-away' }}</small>
                        </td>
                        <td>{{ $trx->details->sum('quantity') }} item</td>
                        <td class="fw-bold text-success">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}
                        </td>
                        <td>
                            @if ($trx->payment_status == 'paid')
                            <span
                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Paid</span>
                            @elseif($trx->payment_status == 'pending')
                            <span
                                class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3">Pending</span>
                            @else
                            <span
                                class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">{{ ucfirst($trx->payment_status) }}</span>
                            @endif
                        </td>
                        <td>
                            <span
                                class="badge bg-secondary rounded-pill px-3">{{ ucfirst($trx->order_status) }}</span>
                        </td>
                        <td class="pe-4 text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary shadow-sm"
                                    data-bs-toggle="modal" data-bs-target="#modal-{{ $trx->id }}">
                                    <i class="bi bi-eye"></i> Detail
                                </button>
                                @if ($trx->payment_status == 'pending')
                                <form action="{{ route('admin.transactions.cancel', $trx) }}" method="POST"
                                    class="d-inline cancel-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger shadow-sm btn-cancel-order"
                                        data-order-code="{{ $trx->order_code }}">
                                        <i class="bi bi-x-circle"></i> Batalkan
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt display-4 opacity-25 d-block mb-3"></i>
                            Tidak ada data transaksi yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($transactions as $trx)
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
                        <div class="text-muted small">
                            {{ $trx->created_at->timezone('Asia/Makassar')->format('d F Y, H:i:s') }}
                        </div>
                    </div>
                    <div class="bg-light p-3 rounded-3 mb-4">
                        <div class="row text-sm">
                            <div class="col-6 text-muted">Pemesan</div>
                            <div class="col-6 fw-bold text-end">{{ $trx->customer_name }}</div>
                            <div class="col-6 text-muted">Meja</div>
                            <div class="col-6 fw-bold text-end">{{ $trx->table_number ?: 'Take Away' }}</div>
                            <div class="col-6 text-muted">Tipe Bayar</div>
                            <div class="col-6 fw-bold text-end text-uppercase">{{ $trx->payment_type ?: '-' }}
                            </div>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-3 border-bottom pb-2">Item Pesanan</h6>
                    <div class="mb-3">
                        @foreach ($trx->details as $item)
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light">
                            <div>
                                <span
                                    class="fw-bold me-2">{{ $item->quantity }}x</span>{{ $item->product_name }}
                                @if ($item->notes)
                                <div class="text-muted small ms-4 fst-italic"><i
                                        class="bi bi-arrow-return-right me-1"></i>{{ $item->notes }}
                                </div>
                                @endif
                            </div>
                            <div class="fw-medium text-end">Rp
                                {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if ($trx->notes)
                    <div class="alert alert-warning py-2 small mb-3"><i
                            class="bi bi-card-text me-2"></i><strong>Catatan:</strong> {{ $trx->notes }}
                    </div>
                    @endif
                    <div class="d-flex justify-content-between fs-5 border-top pt-3">
                        <span class="fw-bold text-muted">Total</span>
                        <span class="fw-bold" style="color: #c97d20;">Rp
                            {{ number_format($trx->total_amount, 0, ',', '.') }}</span>
                    </div>

                    <div id="receipt-{{ $trx->id }}" class="receipt-print" aria-hidden="true">
                        <div class="receipt-header">
                            <h1>Koriru Coffee</h1>
                            <div>Jl. Soekarno Hatta, Tondo, Kota Palu, Sulawesi Tengah</div>
                            <div class='mb-2'>Telp: 0813-9609-332</div>
                            <div>Struk Pembayaran</div>
                        </div>
                        <div class="receipt-divider">--------------------------------</div>
                        <div class="receipt-meta">
                            <div>No: {{ $trx->order_code }}</div>
                            <div>{{ $trx->created_at->timezone('Asia/Makassar')->format('d/m/Y H:i') }}</div>
                            <div>Pemesan: {{ $trx->customer_name }}</div>
                            <div>Meja: {{ $trx->table_number ?: 'Take Away' }}</div>
                        </div>
                        <div class="receipt-divider">--------------------------------</div>
                        @foreach ($trx->details as $item)
                        <div class="receipt-item">
                            <div>{{ $item->quantity }}x {{ $item->product_name }}</div>
                            <div>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                        @endforeach
                        <div class="receipt-divider">--------------------------------</div>
                        <div class="receipt-total"><span>Subtotal</span><span>Rp
                                {{ number_format($trx->subtotal, 0, ',', '.') }}</span></div>
                        @if ($trx->tax_amount > 0)
                        <div class="receipt-total"><span>Pajak</span><span>Rp
                                {{ number_format($trx->tax_amount, 0, ',', '.') }}</span></div>
                        @endif
                        <div class="receipt-total receipt-grand-total"><span>TOTAL</span><span>Rp
                                {{ number_format($trx->total_amount, 0, ',', '.') }}</span></div>
                        <div class="receipt-meta receipt-payment">
                            <div>Pembayaran: {{ strtoupper($trx->payment_type ?: '-') }}</div>
                            <div>Status: {{ strtoupper($trx->payment_status) }}</div>
                        </div>
                        <div class="receipt-footer">Terima kasih telah berkunjung</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-outline-primary"
                        onclick="printReceipt('receipt-{{ $trx->id }}')">
                        <i class="bi bi-printer me-1"></i> Cetak Struk
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @if ($transactions->hasPages())
    <div class="card-footer bg-white pt-4 pb-3 border-top-0">
        {{ $transactions->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- Modal Konfirmasi Hapus Massal -->
<div class="modal fade" id="modal-bulk-delete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Konfirmasi
                    Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin menghapus <strong id="confirm-count">0</strong> transaksi
                    yang dipilih?</p>
                <p class="text-muted small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Data yang dihapus tidak
                    dapat dikembalikan.</p>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn-confirm-delete" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Batalkan Pesanan -->
<div class="modal fade" id="modal-cancel-order" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-warning"><i class="bi bi-exclamation-triangle me-2"></i>Batalkan
                    Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Apakah Anda yakin ingin membatalkan pesanan <strong
                        id="cancel-order-code"></strong>?</p>
                <p class="text-muted small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i>Pembayaran di Midtrans
                    juga akan dibatalkan.</p>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btn-confirm-cancel" class="btn btn-danger btn-sm">
                    <i class="bi bi-x-circle me-1"></i> Ya, Batalkan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .receipt-print {
        display: none;
    }

    /* Highlight baris saat checkbox dicentang */
    tr.row-selected {
        background-color: rgba(13, 110, 253, 0.05) !important;
    }

    /* Animasi untuk toolbar bulk */
    #bulk-toolbar {
        transition: opacity 0.2s ease;
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

        .receipt-item>div:first-child {
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
    // ─── Select All / Row Checkbox Logic ─────────────────────────────────
    const selectAllCheckbox = document.getElementById('select-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkToolbar = document.getElementById('bulk-toolbar');
    const selectedCountEl = document.getElementById('selected-count');
    const confirmCountEl = document.getElementById('confirm-count');
    const bulkIdsContainer = document.getElementById('bulk-ids-container');

    function updateBulkToolbar() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        const count = checked.length;

        selectedCountEl.textContent = count;
        confirmCountEl.textContent = count;

        if (count > 0) {
            bulkToolbar.classList.remove('d-none');
        } else {
            bulkToolbar.classList.add('d-none');
        }

        // Update "select all" checkbox state
        if (rowCheckboxes.length > 0) {
            selectAllCheckbox.checked = count === rowCheckboxes.length;
            selectAllCheckbox.indeterminate = count > 0 && count < rowCheckboxes.length;
        }

        // Highlight rows
        rowCheckboxes.forEach(cb => {
            const row = cb.closest('tr');
            if (cb.checked) {
                row.classList.add('row-selected');
            } else {
                row.classList.remove('row-selected');
            }
        });
    }

    // Select All checkbox
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(cb => {
                cb.checked = selectAllCheckbox.checked;
            });
            updateBulkToolbar();
        });
    }

    // Individual row checkboxes
    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkToolbar);
    });

    // Deselect All button
    document.getElementById('btn-deselect-all')?.addEventListener('click', function() {
        selectAllCheckbox.checked = false;
        selectAllCheckbox.indeterminate = false;
        rowCheckboxes.forEach(cb => {
            cb.checked = false;
        });
        updateBulkToolbar();
    });

    // ─── Bulk Delete Confirmation ────────────────────────────────────────
    const bulkDeleteModal = new bootstrap.Modal(document.getElementById('modal-bulk-delete'));

    document.getElementById('btn-bulk-delete')?.addEventListener('click', function() {
        const checked = document.querySelectorAll('.row-checkbox:checked');
        if (checked.length === 0) return;
        bulkDeleteModal.show();
    });

    document.getElementById('btn-confirm-delete')?.addEventListener('click', function() {
        // Populate hidden inputs with selected IDs
        bulkIdsContainer.innerHTML = '';
        const checked = document.querySelectorAll('.row-checkbox:checked');
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            bulkIdsContainer.appendChild(input);
        });

        bulkDeleteModal.hide();
        document.getElementById('bulk-delete-form').submit();
    });

    // ─── Cancel Order Confirmation ───────────────────────────────────────
    let activeCancelForm = null;
    const cancelModal = new bootstrap.Modal(document.getElementById('modal-cancel-order'));
    const cancelOrderCodeEl = document.getElementById('cancel-order-code');

    document.querySelectorAll('.btn-cancel-order').forEach(btn => {
        btn.addEventListener('click', function() {
            activeCancelForm = this.closest('.cancel-form');
            cancelOrderCodeEl.textContent = this.getAttribute('data-order-code');
            cancelModal.show();
        });
    });

    document.getElementById('btn-confirm-cancel')?.addEventListener('click', function() {
        if (activeCancelForm) {
            cancelModal.hide();
            activeCancelForm.submit();
        }
    });

    // ─── Print Receipt ───────────────────────────────────────────────────
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