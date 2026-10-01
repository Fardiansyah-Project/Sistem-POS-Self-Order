let currentTransactions = [];
let currentPage = 1;

$(document).ready(function () {
    loadTransactions();

    // Handle filter submit
    $('#filter-form').submit(function (e) {
        e.preventDefault();
        loadTransactions(1);
    });

    // Handle pagination
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        loadTransactions(page);
    });

    // Handle check all
    $('#check-all').change(function () {
        $('.tx-check').prop('checked', $(this).prop('checked'));
        updateBulkToolbar();
    });

    $(document).on('change', '.tx-check', function () {
        updateBulkToolbar();
        if ($('.tx-check:checked').length === $('.tx-check').length) {
            $('#check-all').prop('checked', true);
        } else {
            $('#check-all').prop('checked', false);
        }
    });

    // Handle bulk delete
    $('#btn-bulk-delete').click(function () {
        let ids = [];
        $('.tx-check:checked').each(function () {
            ids.push($(this).val());
        });

        if (ids.length === 0) return;

        confirmAction('Hapus Transaksi', `Anda yakin ingin menghapus ${ids.length} transaksi yang dipilih?`, function () {
            bulkDelete(ids);
        });
    });
});

function loadTransactions(page = 1) {
    currentPage = page;
    const params = $('#filter-form').serialize() + '&page=' + page;

    $.ajax({
        url: API_URL + '/transactions?' + params,
        type: 'GET',
        success: function (res) {
            currentTransactions = res.data;
            renderTable(res.data);
            renderPagination(res);
            $('#check-all').prop('checked', false);
            updateBulkToolbar();
        },
        error: function (xhr) {
            showAlert('error', 'Gagal memuat data transaksi.');
        }
    });
}

function renderTable(transactions) {
    const tbody = $('#transactions-tbody');
    tbody.empty();

    if (!transactions || transactions.length === 0) {
        tbody.html('<tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada data transaksi.</td></tr>');
        return;
    }

    transactions.forEach(tx => {
        // Badge Payment
        let paymentBadge = tx.payment_status === 'paid' 
            ? '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2"><i class="bi bi-check-circle me-1"></i>Lunas</span>'
            : '<span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2">Pending</span>';

        // Badge Order
        let orderBadge = '';
        switch(tx.order_status) {
            case 'waiting': orderBadge = '<span class="badge bg-secondary rounded-pill">Menunggu</span>'; break;
            case 'processing': orderBadge = '<span class="badge bg-primary rounded-pill">Diproses</span>'; break;
            case 'ready': orderBadge = '<span class="badge bg-info rounded-pill text-dark">Siap</span>'; break;
            case 'completed': orderBadge = '<span class="badge bg-success rounded-pill">Selesai</span>'; break;
            case 'cancelled': orderBadge = '<span class="badge bg-danger rounded-pill">Batal</span>'; break;
        }

        // Action Buttons
        let btnView = `<button class="btn btn-sm btn-outline-primary" onclick="showReceipt(${tx.id})" title="Lihat Struk"><i class="bi bi-receipt"></i></button>`;
        let btnCancel = (tx.order_status !== 'completed' && tx.order_status !== 'cancelled')
            ? `<button class="btn btn-sm btn-outline-warning" onclick="cancelTransaction(${tx.id})" title="Batalkan Pesanan"><i class="bi bi-x-circle"></i></button>`
            : '';

        let dateFormatted = new Date(tx.created_at).toLocaleString('id-ID', {day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'});

        let tr = `
            <tr>
                <td class="ps-4">
                    <input class="form-check-input tx-check" type="checkbox" value="${tx.id}">
                </td>
                <td><span class="fw-bold font-monospace text-muted">${tx.order_code}</span></td>
                <td>
                    <div class="fw-bold">${tx.customer_name}</div>
                    ${tx.table_number ? `<small class="text-muted"><i class="bi bi-shop me-1"></i>Meja ${tx.table_number}</small>` : '<small class="text-muted">Takeaway</small>'}
                </td>
                <td>${dateFormatted}</td>
                <td class="fw-bold text-brand">${formatRupiah(tx.total_amount)}</td>
                <td>${paymentBadge}</td>
                <td>${orderBadge}</td>
                <td class="text-center pe-4">
                    <div class="d-flex justify-content-center gap-1">
                        ${btnView}
                        ${btnCancel}
                    </div>
                </td>
            </tr>
        `;
        tbody.append(tr);
    });
}

function renderPagination(res) {
    let html = '';
    if (res.last_page > 1) {
        html += '<ul class="pagination pagination-sm mb-0">';
        for (let i = 1; i <= res.last_page; i++) {
            let active = i === res.current_page ? 'active' : '';
            html += `<li class="page-item ${active}"><a class="page-link" href="?page=${i}">${i}</a></li>`;
        }
        html += '</ul>';
    }
    $('#pagination-container').html(html);
}

function updateBulkToolbar() {
    const count = $('.tx-check:checked').length;
    if (count > 0) {
        $('#selected-count').text(count);
        $('#bulk-toolbar').removeClass('d-none').addClass('d-flex');
    } else {
        $('#bulk-toolbar').removeClass('d-flex').addClass('d-none');
    }
}

function bulkDelete(ids) {
    $.ajax({
        url: API_URL + '/transactions/bulk-destroy',
        type: 'DELETE',
        data: JSON.stringify({ ids: ids }),
        contentType: 'application/json',
        success: function (res) {
            showAlert('success', res.message);
            loadTransactions(currentPage);
        },
        error: function (xhr) {
            showAlert('error', 'Gagal menghapus transaksi.');
        }
    });
}

function cancelTransaction(id) {
    confirmAction('Batalkan Pesanan', 'Apakah Anda yakin pesanan ini dibatalkan? (Aksi ini tidak mengembalikan dana secara sistem)', function () {
        $.ajax({
            url: API_URL + '/transactions/' + id + '/cancel',
            type: 'PATCH',
            success: function (res) {
                showAlert('success', res.message);
                loadTransactions(currentPage);
            },
            error: function (xhr) {
                showAlert('error', xhr.responseJSON?.message || 'Gagal membatalkan transaksi.');
            }
        });
    });
}

function showReceipt(id) {
    // Cari data transaksi dari array
    let tx = null;
    $.ajax({
        url: API_URL + '/transactions/' + id,
        type: 'GET',
        async: false,
        success: function(res) { tx = res.data; }
    });

    if (!tx) return;

    let itemsHtml = '';
    tx.details.forEach(d => {
        itemsHtml += `
            <div class="d-flex justify-content-between small mb-1">
                <div>${d.product_name} x ${d.quantity}</div>
                <div>${formatRupiah(d.subtotal)}</div>
            </div>
            ${d.notes ? `<div class="small text-muted fst-italic ms-2">- ${d.notes}</div>` : ''}
        `;
    });

    let dateFormatted = new Date(tx.created_at).toLocaleString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'});

    let html = `
        <div class="text-center mb-3">
            <h5 class="fw-bold mb-0">KORIRO COFFEE</h5>
            <small class="text-muted">Struk Pesanan</small>
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
                <span>${tx.table_number || '-'}</span>
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

function printReceipt() {
    window.print();
}
