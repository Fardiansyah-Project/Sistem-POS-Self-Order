$(document).ready(function () {
    loadActiveOrders();

    // Auto Refresh Page every 15 seconds to fetch new orders
    setInterval(loadActiveOrders, 15000);
});

function loadActiveOrders() {
    $.ajax({
        url: API_URL + '/kitchen',
        type: 'GET',
        success: function (res) {
            renderOrders(res.data);
        },
        error: function () {
            $('#kitchen-container').html(`
                <div class="col-12 text-center py-5">
                    <p class="text-danger">Gagal memuat pesanan dapur. Coba lagi nanti.</p>
                </div>
            `);
        }
    });
}

function renderOrders(orders) {
    const container = $('#kitchen-container');
    container.empty();

    if (!orders || orders.length === 0) {
        container.html(`
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="display-1 text-muted opacity-25 mb-3"><i class="bi bi-cup-hot"></i></div>
                    <h4 class="text-muted fw-bold">Dapur Sedang Santai</h4>
                    <p class="text-muted">Tidak ada pesanan aktif yang menunggu diproses.</p>
                </div>
            </div>
        `);
        return;
    }

    orders.forEach(order => {
        let borderColor = order.order_status === 'ready' ? 'border-success border border-2' : '';
        let headerColor = '';
        if (order.order_status === 'processing') headerColor = 'bg-primary';
        else if (order.order_status === 'ready') headerColor = 'bg-success';
        else headerColor = 'bg-secondary';

        let itemsHtml = '';
        order.details.forEach(item => {
            itemsHtml += `
                <li class="list-group-item px-0 py-2 border-light">
                    <div class="d-flex align-items-start">
                        <span class="badge bg-dark rounded-pill me-2 fs-6 mt-1">${item.quantity}x</span>
                        <div>
                            <span class="fw-bold">${item.product_name}</span>
                            ${item.notes ? `<br><small class="text-danger fw-medium fst-italic"><i class="bi bi-exclamation-circle me-1"></i>${item.notes}</small>` : ''}
                        </div>
                    </div>
                </li>
            `;
        });

        let notesHtml = order.notes ? `
            <div class="alert alert-warning py-2 mb-0 mt-3 small">
                <strong>Catatan:</strong> ${order.notes}
            </div>
        ` : '';

        let actionBtn = '';
        if (order.order_status === 'waiting') {
            actionBtn = `<button onclick="updateStatus(${order.id}, 'processing')" class="btn btn-primary w-100 fw-bold shadow-sm py-2"><i class="bi bi-play-circle me-1"></i> Mulai Proses</button>`;
        } else if (order.order_status === 'processing') {
            actionBtn = `<button onclick="updateStatus(${order.id}, 'ready')" class="btn btn-success w-100 fw-bold shadow-sm py-2"><i class="bi bi-check2-circle me-1"></i> Tandai Siap Diambil</button>`;
        } else if (order.order_status === 'ready') {
            actionBtn = `<button onclick="updateStatus(${order.id}, 'completed')" class="btn btn-outline-secondary w-100 fw-bold py-2"><i class="bi bi-box-arrow-right me-1"></i> Selesaikan Pesanan</button>`;
        }

        let html = `
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100 overflow-hidden ${borderColor}">
                    <div class="card-header text-white border-bottom-0 py-3 ${headerColor}">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold font-monospace fs-5">${order.order_code}</span>
                            <span class="badge bg-white text-dark rounded-pill shadow-sm">${order.time_ago}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <div>
                                <small class="text-muted d-block text-uppercase">Pemesan</small>
                                <span class="fw-bold fs-5">${order.customer_name}</span>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block text-uppercase">Meja</small>
                                <span class="fw-bold fs-5 text-brand" style="color: #c97d20;">${order.table_number || 'TAKE AWAY'}</span>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush mb-3">
                            ${itemsHtml}
                        </ul>
                        ${notesHtml}
                    </div>
                    <div class="card-footer bg-white border-top pb-3 pt-3">
                        ${actionBtn}
                    </div>
                </div>
            </div>
        `;
        container.append(html);
    });
}

function updateStatus(id, newStatus) {
    $.ajax({
        url: API_URL + '/kitchen/' + id + '/status',
        type: 'PATCH',
        data: { order_status: newStatus },
        success: function (res) {
            showAlert('success', res.message);
            loadActiveOrders(); // reload
        },
        error: function () {
            showAlert('error', 'Gagal memperbarui status pesanan.');
        }
    });
}
