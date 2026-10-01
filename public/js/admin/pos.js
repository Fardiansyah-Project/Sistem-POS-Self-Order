let cart = [];
const taxRate = 0.11;
let grandTotal = 0;

$(document).ready(function () {
    loadProducts();

    $('#category-filter').change(function () {
        loadProducts($(this).val());
    });

    $('#amount_paid').on('input', calculateChange);

    $('#checkout-form').submit(function (e) {
        e.preventDefault();
        submitOrder();
    });

    // Event delegation for cart actions
    $('#cart-items-container').on('click', '.btn-plus', function() {
        updateQty($(this).data('id'), 1);
    });
    $('#cart-items-container').on('click', '.btn-minus', function() {
        updateQty($(this).data('id'), -1);
    });
    $('#cart-items-container').on('click', '.btn-delete', function() {
        removeFromCart($(this).data('id'));
    });
    $('#cart-items-container').on('input', '.cart-item-notes', function() {
        updateNotes($(this).data('id'), $(this).val());
    });
});

function loadProducts(categoryId = '') {
    let url = API_URL + '/pos/products';
    if (categoryId) url += '?category=' + categoryId;

    $.ajax({
        url: url,
        type: 'GET',
        success: function (res) {
            // Populate category dropdown only once (if not populated)
            if ($('#category-filter option').length <= 1) {
                res.data.categories.forEach(cat => {
                    $('#category-filter').append(`<option value="${cat.id}">${cat.name}</option>`);
                });
            }

            renderCatalog(res.data.products);
        },
        error: function () {
            $('#products-container').html('<div class="col-12 text-center text-danger">Gagal memuat produk.</div>');
        }
    });
}

function renderCatalog(products) {
    const container = $('#products-container');
    container.empty();

    if (products.length === 0) {
        container.html(`
            <div class="col-12 text-center py-5">
                <i class="bi bi-box-seam display-4 text-muted mb-3 d-block opacity-25"></i>
                <p class="text-muted">Tidak ada produk yang tersedia.</p>
            </div>
        `);
        return;
    }

    products.forEach(p => {
        let imageHtml = p.image_url 
            ? `<img src="${p.image_url}" class="card-img-top" alt="${p.name}" style="height: 140px; object-fit: cover;">`
            : `<div class="card-img-top bg-secondary d-flex align-items-center justify-content-center text-white" style="height: 140px;"><i class="bi bi-image fs-1 opacity-50"></i></div>`;
        
        let outOfStockHtml = !p.is_available 
            ? `<div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center"><span class="badge bg-danger fs-6">Habis</span></div>`
            : '';

        let html = `
            <div class="col-md-4 col-sm-6">
                <div class="card h-100 border-0 shadow-sm product-card">
                    <div class="position-relative">
                        ${imageHtml}
                        ${outOfStockHtml}
                    </div>
                    <div class="card-body p-2 d-flex flex-column text-center">
                        <h6 class="card-title fw-bold mb-1 text-truncate" title="${p.name}">${p.name}</h6>
                        <p class="card-text text-brand fw-bold mb-2">${formatRupiah(p.price)}</p>
                        <div class="mt-auto">
                            <button type="button" class="btn btn-sm btn-primary w-100 fw-bold shadow-sm"
                                onclick="addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${p.price})"
                                ${!p.is_available ? 'disabled' : ''}>
                                <i class="bi bi-cart-plus me-1"></i> Tambah
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.append(html);
    });
}

function addToCart(id, name, price) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({ id, name, price, quantity: 1, notes: '' });
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
    if (item) item.notes = note;
}

function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);
    renderCart();
}

function renderCart() {
    const container = $('#cart-items-container');
    const emptyMsg = $('#empty-cart-msg');

    if (cart.length === 0) {
        container.html('');
        emptyMsg.show();
        updateSummary(0);
        return;
    }

    emptyMsg.hide();
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
    container.html(html);
    updateSummary(subtotal);
}

function updateSummary(subtotal) {
    const tax = Math.round(subtotal * taxRate);
    grandTotal = subtotal + tax;

    $('#summary-subtotal').text(formatRupiah(subtotal));
    $('#summary-tax').text(formatRupiah(tax));
    $('#summary-total').text(formatRupiah(grandTotal));

    calculateChange();
}

function calculateChange() {
    const paid = parseFloat($('#amount_paid').val()) || 0;
    const kembalianContainer = $('#kembalian-container');
    const kembalianText = $('#kembalian-text');

    if (paid > 0) {
        kembalianContainer.attr('style', 'display: flex !important');
        const change = paid - grandTotal;
        if (change >= 0) {
            kembalianText.text(formatRupiah(change)).attr('class', 'text-success');
        } else {
            kembalianText.text('Kurang ' + formatRupiah(Math.abs(change))).attr('class', 'text-danger');
        }
    } else {
        kembalianContainer.attr('style', 'display: none !important');
    }
}

function submitOrder() {
    if (cart.length === 0) {
        showAlert('warning', 'Keranjang belanja masih kosong!');
        return;
    }

    const paid = parseFloat($('#amount_paid').val()) || 0;
    if (paid < grandTotal) {
        showAlert('warning', 'Nominal uang yang diterima kurang dari total tagihan!');
        return;
    }

    let data = {
        customer_name: $('#customer_name').val(),
        table_number: $('#table_number').val(),
        notes: $('#order_notes').val(),
        payment_type: $('#payment_type').val(),
        amount_paid: paid,
        items: JSON.stringify(cart)
    };

    let btn = $('#btn-submit');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');

    $.ajax({
        url: API_URL + '/pos/store',
        type: 'POST',
        data: data,
        success: function (res) {
            showAlert('success', res.message);
            showReceipt(res.data.transaction);
        },
        error: function (xhr) {
            handleValidationErrors(xhr);
            btn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Proses Pesanan');
        }
    });
}

function showReceipt(tx) {
    let itemsHtml = '';
    tx.details.forEach(d => {
        itemsHtml += `
            <div class="d-flex justify-content-between small mb-1">
                <div>${d.product_name} x ${d.quantity}</div>
                <div>${formatRupiah(d.subtotal)}</div>
            </div>
        `;
    });

    let dateFormatted = new Date(tx.created_at).toLocaleString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'});

    let html = `
        <div class="text-center mb-3">
            <h5 class="fw-bold mb-0">KORIRO COFFEE</h5>
            <small class="text-muted">Struk Pembayaran (POS)</small>
        </div>
        <div class="border-top border-bottom py-2 border-dashed mb-3 small">
            <div class="d-flex justify-content-between">
                <span>Kode:</span>
                <span class="fw-bold">${tx.order_code}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Pelanggan:</span>
                <span>${tx.customer_name}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Meja:</span>
                <span>${tx.table_number || 'Take Away'}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Tanggal:</span>
                <span>${dateFormatted}</span>
            </div>
        </div>
        <div class="mb-3">
            <div class="fw-bold small mb-2 border-bottom pb-1">Pesanan:</div>
            ${itemsHtml}
        </div>
        <div class="border-top pt-2 small">
            <div class="d-flex justify-content-between">
                <span>Subtotal</span>
                <span>${formatRupiah(tx.subtotal)}</span>
            </div>
            <div class="d-flex justify-content-between text-muted">
                <span>Pajak (11%)</span>
                <span>${formatRupiah(tx.tax_amount)}</span>
            </div>
            <div class="d-flex justify-content-between fw-bold fs-6 mt-1 border-top pt-1">
                <span>TOTAL</span>
                <span>${formatRupiah(tx.total_amount)}</span>
            </div>
            <div class="d-flex justify-content-between text-muted mt-2">
                <span>Pembayaran</span>
                <span class="text-uppercase">${tx.payment_type}</span>
            </div>
        </div>
        <div class="text-center mt-4 small text-muted">
            <p class="mb-0">Terima kasih atas kunjungan Anda.</p>
        </div>
    `;

    $('#receipt-content').html(html);
    const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
    modal.show();
}

function resetPOS() {
    // Reset Form
    $('#checkout-form')[0].reset();
    $('#amount_paid').trigger('input');
    
    // Reset Cart
    cart = [];
    renderCart();

    // Reset Button
    $('#btn-submit').prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Proses Pesanan');
}
