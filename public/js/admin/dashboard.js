$(document).ready(function () {
    loadDashboardData();
    loadOrderAvailability();
    $('#toggle-order-status').on('click', toggleOrderAvailability);
});

function loadOrderAvailability() {
    $.get(API_URL + '/order-availability')
        .done(function (res) {
            renderOrderAvailability(res.is_open);
        })
        .fail(function () {
            showAlert('error', 'Gagal memuat status pemesanan customer.');
        });
}

function renderOrderAvailability(isOpen) {
    const button = $('#toggle-order-status');
    button.attr('data-open', isOpen ? '1' : '0');
    button.toggleClass('btn-danger', isOpen).toggleClass('btn-success', !isOpen);
    button.html(isOpen
        ? '<i class="bi bi-lock-fill me-1"></i> Tutup Order Customer'
        : '<i class="bi bi-unlock-fill me-1"></i> Buka Order Customer');
}

function toggleOrderAvailability() {
    const button = $('#toggle-order-status');
    const currentOpen = button.attr('data-open') === '1';
    const nextOpen = !currentOpen;
    const prompt = nextOpen
        ? 'Buka kembali pemesanan customer?'
        : 'Tutup pemesanan customer? Customer tidak dapat menambahkan item atau checkout.';

    confirmAction(nextOpen ? 'Buka Order' : 'Tutup Order', prompt, function () {
        button.prop('disabled', true);
        $.ajax({
            url: API_URL + '/order-availability',
            type: 'PATCH',
            data: { is_open: nextOpen ? 1 : 0 },
            success: function (res) {
                renderOrderAvailability(res.is_open);
                showAlert('success', res.message);
            },
            error: function (xhr) {
                showAlert('error', xhr.responseJSON?.message || 'Gagal mengubah status order.');
            },
            complete: function () {
                button.prop('disabled', false);
            }
        });
    });
}

function loadDashboardData() {
    $.ajax({
        url: API_URL + '/dashboard',
        type: 'GET',
        success: function (res) {
            const data = res.data;

            // Update Summary Cards
            $('#stat-revenue').text(formatRupiah(data.revenue_today || 0));
            $('#stat-orders').text(data.orders_today || 0);
            $('#stat-critical-stock').text(data.critical_ingredients ? data.critical_ingredients.length : 0);

            // Render Top Products
            renderTopProducts(data.top_products);

            // Render Critical Stock
            renderCriticalStock(data.critical_ingredients);

            // Render Chart
            renderSalesChart(data.sales_chart);
        },
        error: function (xhr) {
            showAlert('error', 'Gagal memuat data dashboard.');
        }
    });
}

function renderTopProducts(products) {
    const container = $('#top-products-list');
    container.empty();

    if (!products || products.length === 0) {
        container.html('<li class="list-group-item text-center text-muted py-3">Belum ada penjualan bulan ini.</li>');
        return;
    }

    products.forEach((prod, index) => {
        container.append(`
            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                <div>
                    <span class="badge bg-secondary rounded-circle me-2">${index + 1}</span>
                    ${prod.product_name}
                </div>
                <span class="badge bg-primary rounded-pill">${prod.total_sold} qty</span>
            </li>
        `);
    });
}

function renderCriticalStock(ingredients) {
    const container = $('#critical-stock-container');
    container.empty();

    if (!ingredients || ingredients.length === 0) {
        container.html('<p class="text-success mb-0 small"><i class="bi bi-check-circle me-1"></i> Semua stok bahan baku aman.</p>');
        return;
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-sm table-borderless small mb-0">
                <tbody>
    `;

    ingredients.forEach(ing => {
        html += `
            <tr>
                <td>${ing.name}</td>
                <td class="text-end text-danger fw-bold">${parseFloat(ing.stock_quantity)} ${ing.unit}</td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
        <a href="/cms/admin/ingredients" class="btn btn-sm btn-outline-danger w-100 mt-3">Restock Sekarang</a>
    `;

    container.html(html);
}

let salesChartInstance = null;

function renderSalesChart(salesData) {
    if (!salesData || !salesData.labels || !salesData.data) return;

    const ctx = document.getElementById('salesChart').getContext('2d');
    
    if (salesChartInstance) {
        salesChartInstance.destroy();
    }

    salesChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.labels,
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: salesData.data,
                borderColor: '#c97d20',
                backgroundColor: 'rgba(201, 125, 32, 0.1)',
                borderWidth: 3,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#c97d20'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + (value/1000) + 'k';
                        }
                    }
                }
            }
        }
    });
}
