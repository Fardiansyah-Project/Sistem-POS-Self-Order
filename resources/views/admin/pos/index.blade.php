@extends('layouts.admin')

@section('title', 'Kasir POS')

@section('content')
<div class="row g-3">
    <!-- Left Panel: Products -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white pt-3 pb-2 border-bottom-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-uppercase"><i class="bi bi-box-seam me-2 text-primary"></i>Katalog Menu</h6>
                <!-- Category Filter -->
                <form action="{{ route('admin.pos.index') }}" method="GET" class="d-flex" id="category-form">
                    <select name="category" class="form-select form-select-sm shadow-sm" onchange="document.getElementById('category-form').submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body bg-light rounded-bottom p-3" style="max-height: calc(100vh - 180px); overflow-y: auto;">
                <div class="row g-3">
                    @forelse($products as $product)
                    <div class="col-md-4 col-sm-6">
                        <div class="card h-100 border-0 shadow-sm product-card">
                            <div class="position-relative">
                                @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 140px; object-fit: cover;">
                                @else
                                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center text-white" style="height: 140px;">
                                    <i class="bi bi-image fs-1 opacity-50"></i>
                                </div>
                                @endif
                                @if(!$product->is_available)
                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center">
                                    <span class="badge bg-danger fs-6">Habis</span>
                                </div>
                                @endif
                            </div>
                            <div class="card-body p-2 d-flex flex-column text-center">
                                <h6 class="card-title fw-bold mb-1 text-truncate" title="{{ $product->name }}">{{ $product->name }}</h6>
                                <p class="card-text text-brand fw-bold mb-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>

                                <div class="mt-auto">
                                    <button type="button" class="btn btn-sm btn-primary w-100 fw-bold shadow-sm btn-add-cart"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $product->price }}"
                                        {{ !$product->is_available ? 'disabled' : '' }}>
                                        <i class="bi bi-cart-plus me-1"></i> Tambah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <i class="bi bi-box-seam display-4 text-muted mb-3 d-block opacity-25"></i>
                        <p class="text-muted">Tidak ada produk yang tersedia.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel: Cart & Checkout -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100 d-flex flex-column">
            <div class="card-header bg-white pt-3 pb-2 border-bottom-0">
                <h6 class="fw-bold mb-0 text-uppercase"><i class="bi bi-cart3 me-2 text-primary"></i>Keranjang Pesanan</h6>
            </div>

            <div class="card-body p-0 flex-grow-1 d-flex flex-column" style="max-height: calc(100vh - 450px); overflow-y: auto;">
                <div id="cart-items-container" class="w-100"></div>

                <div class="text-center py-5 text-muted flex-grow-1 mt-4" id="empty-cart-msg">
                    <i class="bi bi-cart-x display-4 d-block opacity-25 mb-3"></i>
                    Keranjang masih kosong
                </div>
            </div>

            <div class="card-footer bg-white p-3 border-top">
                <!-- Order Summary -->
                <div class="d-flex justify-content-between mb-1 small text-muted">
                    <span>Subtotal</span>
                    <span id="summary-subtotal">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small text-muted">
                    <span>PPN (11%)</span>
                    <span id="summary-tax">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between mb-3 fw-bold fs-5 text-brand">
                    <span>Total Tagihan</span>
                    <span id="summary-total">Rp 0</span>
                </div>

                <!-- Checkout Form -->
                <form action="{{ route('admin.pos.store') }}" method="POST" id="checkout-form">
                    @csrf
                    <input type="hidden" name="items" id="form-items-json">

                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm" name="customer_name" placeholder="Nama Pelanggan *" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm" name="table_number" placeholder="Nomor Meja (Opsional)">
                    </div>
                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm" name="notes" placeholder="Catatan Transaksi (Opsional)">
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <select name="payment_type" class="form-select form-select-sm" required>
                                <option value="cash">Tunai (Cash)</option>
                                <option value="qris">QRIS / e-Wallet</option>
                                <option value="bank_transfer">Transfer Bank</option>
                                <option value="edc">Kartu Debit/Kredit</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <input type="number" class="form-control form-control-sm" name="amount_paid" id="amount_paid" placeholder="Uang Diterima *" required min="0">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-3 small fw-bold" id="kembalian-container" style="display: none !important;">
                        <span>Kembalian:</span>
                        <span id="kembalian-text" class="text-success">Rp 0</span>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm" id="btn-submit">
                        <i class="bi bi-check2-circle me-1"></i> Proses Pesanan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('print_receipt_id'))
@php
$receiptTrx = \App\Models\Transaction::with('details')->find(session('print_receipt_id'));
@endphp
@if($receiptTrx)
<div id="pos-receipt" class="receipt-print" aria-hidden="true">
    <div class="receipt-header">
        <h1>Koriro Coffee</h1>
        <div>Jl. Soekarno Hatta, Tondo, Kota Palu</div>
        <div class='mb-2'>Telp: 0813-9609-332</div>
        <div>Struk Pembayaran (POS)</div>
    </div>
    <div class="receipt-divider">--------------------------------</div>
    <div class="receipt-meta">
        <div>No: {{ $receiptTrx->order_code }}</div>
        <div>{{ $receiptTrx->created_at->timezone('Asia/Makassar')->format('d/m/Y H:i') }}</div>
        <div>Kasir: {{ auth()->user()->name ?? 'Kasir' }}</div>
        <div>Pemesan: {{ $receiptTrx->customer_name }}</div>
        <div>Meja: {{ $receiptTrx->table_number ?: 'Take Away' }}</div>
    </div>
    <div class="receipt-divider">--------------------------------</div>
    @foreach($receiptTrx->details as $item)
    <div class="receipt-item">
        <div>{{ $item->quantity }}x {{ $item->product_name }}</div>
        <div>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
    </div>
    @endforeach
    <div class="receipt-divider">--------------------------------</div>
    <div class="receipt-total"><span>Subtotal</span><span>Rp {{ number_format($receiptTrx->subtotal, 0, ',', '.') }}</span></div>
    @if($receiptTrx->tax_amount > 0)
    <div class="receipt-total"><span>Pajak</span><span>Rp {{ number_format($receiptTrx->tax_amount, 0, ',', '.') }}</span></div>
    @endif
    <div class="receipt-total receipt-grand-total"><span>TOTAL</span><span>Rp {{ number_format($receiptTrx->total_amount, 0, ',', '.') }}</span></div>
    <div class="receipt-meta receipt-payment">
        <div>Pembayaran: {{ strtoupper($receiptTrx->payment_type ?: 'CASH') }}</div>
        <div>Status: LUNAS</div>
    </div>
    <div class="receipt-footer">Terima kasih telah berkunjung</div>
</div>
@endif
@endif

@endsection

@push('styles')
<style>
    .product-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
    }

    .cart-item-qty {
        width: 40px;
        text-align: center;
    }

    /* Print styles */
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
    document.addEventListener("DOMContentLoaded", function() {
        let cart = [];
        const taxRate = 0.11;
        let grandTotal = 0;

        const container = document.getElementById('cart-items-container');
        const emptyMsg = document.getElementById('empty-cart-msg');
        const amountPaidInput = document.getElementById('amount_paid');
        const formItemsJson = document.getElementById('form-items-json');
        const checkoutForm = document.getElementById('checkout-form');
        const btnSubmit = document.getElementById('btn-submit');
        const kembalianContainer = document.getElementById('kembalian-container');
        const kembalianText = document.getElementById('kembalian-text');

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        // Add to cart from product catalog
        document.querySelectorAll('.btn-add-cart').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const id = parseInt(this.dataset.id);
                const name = this.dataset.name;
                const price = parseFloat(this.dataset.price);
                addToCart(id, name, price);
            });
        });

        function addToCart(id, name, price) {
            const existing = cart.find(item => item.id === id);
            if (existing) {
                existing.quantity++;
            } else {
                cart.push({
                    id,
                    name,
                    price,
                    quantity: 1,
                    notes: ''
                });
            }
            renderCart();
        }

        function updateQty(id, delta) {
            const item = cart.find(i => i.id === id);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) {
                    cart = cart.filter(i => i.id !== id);
                }
                renderCart();
            }
        }

        function updateNotes(id, note) {
            const item = cart.find(i => i.id === id);
            if (item) {
                item.notes = note;
            }
        }

        function removeFromCart(id) {
            cart = cart.filter(i => i.id !== id);
            renderCart();
        }

        function renderCart() {
            if (cart.length === 0) {
                container.innerHTML = '';
                emptyMsg.style.display = 'block';
                updateSummary(0);
                return;
            }

            emptyMsg.style.display = 'none';
            let html = '<ul class="list-group list-group-flush">';
            let subtotal = 0;

            cart.forEach(item => {
                const itemSubtotal = item.price * item.quantity;
                subtotal += itemSubtotal;

                html += `
                <li class="list-group-item p-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="fw-bold text-truncate pe-2">${item.name}</div>
                        <div class="fw-medium">${formatRupiah(itemSubtotal)}</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="input-group input-group-sm w-auto">
                            <button class="btn btn-outline-secondary px-2 btn-minus" type="button" data-id="${item.id}">-</button>
                            <input type="text" class="form-control cart-item-qty bg-white" value="${item.quantity}" readonly>
                            <button class="btn btn-outline-secondary px-2 btn-plus" type="button" data-id="${item.id}">+</button>
                        </div>
                        <button class="btn btn-sm btn-link text-danger p-0 btn-delete" type="button" data-id="${item.id}"><i class="bi bi-trash"></i></button>
                    </div>
                    <div class="mt-2">
                        <input type="text" class="form-control form-control-sm border-0 bg-light cart-item-notes" placeholder="Catatan item..." value="${item.notes}" data-id="${item.id}">
                    </div>
                </li>`;
            });

            html += '</ul>';
            container.innerHTML = html;
            updateSummary(subtotal);
        }

        function updateSummary(subtotal) {
            const tax = Math.round(subtotal * taxRate);
            grandTotal = subtotal + tax;

            document.getElementById('summary-subtotal').textContent = formatRupiah(subtotal);
            document.getElementById('summary-tax').textContent = formatRupiah(tax);
            document.getElementById('summary-total').textContent = formatRupiah(grandTotal);

            calculateChange();
        }

        function calculateChange() {
            const paid = parseFloat(amountPaidInput.value) || 0;
            if (paid > 0) {
                kembalianContainer.style.setProperty('display', 'flex', 'important');
                const change = paid - grandTotal;
                if (change >= 0) {
                    kembalianText.textContent = formatRupiah(change);
                    kembalianText.className = 'text-success';
                } else {
                    kembalianText.textContent = 'Kurang ' + formatRupiah(Math.abs(change));
                    kembalianText.className = 'text-danger';
                }
            } else {
                kembalianContainer.style.setProperty('display', 'none', 'important');
            }
        }

        // Event delegation for cart actions
        container.addEventListener('click', function(e) {
            const btnPlus = e.target.closest('.btn-plus');
            if (btnPlus) {
                updateQty(parseInt(btnPlus.dataset.id), 1);
                return;
            }

            const btnMinus = e.target.closest('.btn-minus');
            if (btnMinus) {
                updateQty(parseInt(btnMinus.dataset.id), -1);
                return;
            }

            const btnDelete = e.target.closest('.btn-delete');
            if (btnDelete) {
                removeFromCart(parseInt(btnDelete.dataset.id));
                return;
            }
        });

        // Event delegation for notes
        container.addEventListener('input', function(e) {
            if (e.target.classList.contains('cart-item-notes')) {
                updateNotes(parseInt(e.target.dataset.id), e.target.value);
            }
        });

        amountPaidInput.addEventListener('input', calculateChange);

        checkoutForm.addEventListener('submit', function(e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('Keranjang belanja masih kosong!');
                return;
            }

            const paid = parseFloat(amountPaidInput.value) || 0;
            if (paid < grandTotal) {
                e.preventDefault();
                alert('Nominal uang yang diterima kurang dari total tagihan!');
                return;
            }

            formItemsJson.value = JSON.stringify(cart);
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
        });

        // Auto-print logic
        const receipt = document.getElementById('pos-receipt');
        if (receipt) {
            receipt.classList.add('receipt-active');
            document.body.classList.add('printing-receipt');
            window.setTimeout(() => {
                window.print();
            }, 500);
        }
    });

    window.addEventListener('afterprint', () => {
        document.body.classList.remove('printing-receipt');
        const receipt = document.getElementById('pos-receipt');
        if (receipt) receipt.classList.remove('receipt-active');
    });
</script>
@endpush