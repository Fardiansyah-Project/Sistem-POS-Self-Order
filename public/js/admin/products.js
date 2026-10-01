$(document).ready(function () {
    loadProducts();

    // Handle pagination clicks
    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        loadProducts(page);
    });
});

function loadProducts(page = 1) {
    $.ajax({
        url: API_URL + '/products?page=' + page,
        type: 'GET',
        success: function (res) {
            renderTable(res.data);
            renderPagination(res);
            // console.log('Response Data : ', res);
        },
        error: function (xhr) {
            showAlert('error', 'Gagal memuat daftar produk.');
            $('#products-tbody').html('<tr><td colspan="5" class="text-center py-4 text-danger">Gagal memuat data.</td></tr>');
        }
    });
}

function renderTable(products) {
    const tbody = $('#products-tbody');
    tbody.empty();

    if (!products || products.length === 0) {
        tbody.html('<tr><td colspan="5" class="text-center py-4 text-muted">Belum ada produk.</td></tr>');
        return;
    }

    products.forEach(prod => {
        let imageHtml = prod.image_url 
            ? `<img src="${prod.image_url}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">`
            : `<div class="bg-light rounded me-3 d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;"><i class="bi bi-image"></i></div>`;
            
        let statusHtml = prod.is_available
            ? `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">Tersedia</span>`
            : `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3">Habis / Kosong</span>`;

        let tr = `
            <tr>
                <td class="ps-4 d-flex align-items-center">
                    ${imageHtml}
                    <div>
                        <h6 class="mb-0 fw-bold">${prod.name}</h6>
                        <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">${prod.description || ''}</small>
                    </div>
                </td>
                <td><span class="badge bg-secondary rounded-pill">${prod.category ? prod.category.name : '-'}</span></td>
                <td class="fw-medium text-brand" style="color: #c97d20;">${formatRupiah(prod.price)}</td>
                <td>${statusHtml}</td>
                <td class="text-center pe-4">
                    <div class="d-flex justify-content-center gap-1">
                        <a href="/cms/admin/products/${prod.id}/recipes" class="btn btn-sm btn-outline-info" title="Atur Resep (Bahan Baku)"><i class="bi bi-list-nested"></i></a>
                        <a href="/cms/admin/products/${prod.id}/edit" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="${prod.id}" title="Hapus"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(tr);
    });

    // Attach delete event
    $('.btn-delete').click(function () {
        let id = $(this).data('id');
        confirmAction('Hapus Produk', 'Apakah Anda yakin ingin menghapus produk ini?', function () {
            deleteProduct(id);
        });
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

function deleteProduct(id) {
    $.ajax({
        url: API_URL + '/products/' + id,
        type: 'DELETE',
        success: function (res) {
            showAlert('success', res.message);
            loadProducts();
        },
        error: function (xhr) {
            handleValidationErrors(xhr);
        }
    });
}
