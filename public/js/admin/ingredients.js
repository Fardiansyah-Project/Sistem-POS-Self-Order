let currentPage = 1;

$(document).ready(function () {
    loadIngredients();

    $('#ingredientForm').submit(function (e) {
        e.preventDefault();
        saveIngredient();
    });

    $('#ing-unit').on('input', function() {
        $('.unit-label').text($(this).val() || '-');
    });

    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        loadIngredients(page);
    });
});

let bsModal = null;

function loadIngredients(page = 1) {
    currentPage = page;
    $.ajax({
        url: API_URL + '/ingredients?page=' + page,
        type: 'GET',
        success: function (res) {
            renderTable(res.data, res.from);
            renderPagination(res);
        },
        error: function (xhr) {
            $('#ingredients-tbody').html('<tr><td colspan="6" class="text-center py-4 text-danger">Gagal memuat data.</td></tr>');
            showAlert('error', 'Gagal memuat daftar bahan baku.');
        }
    });
}

function renderTable(ingredients, fromIndex) {
    const tbody = $('#ingredients-tbody');
    tbody.empty();

    if (!ingredients || ingredients.length === 0) {
        tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada bahan baku tercatat.</td></tr>');
        return;
    }

    ingredients.forEach((ing, index) => {
        let isCritical = parseFloat(ing.stock_quantity) <= parseFloat(ing.minimum_stock);
        let rowClass = isCritical ? 'table-danger' : '';
        let criticalBadge = isCritical ? '<span class="badge bg-danger ms-2" style="font-size: 0.65rem;">Kritis!</span>' : '';
        let stockClass = isCritical ? 'text-danger' : 'text-success';

        let tr = `
            <tr class="${rowClass}">
                <td class="ps-4 text-muted">${fromIndex + index}</td>
                <td class="fw-bold">${ing.name} ${criticalBadge}</td>
                <td><span class="badge bg-secondary rounded-pill px-3">${ing.unit}</span></td>
                <td class="fw-bold ${stockClass}">${parseFloat(ing.stock_quantity)} ${ing.unit}</td>
                <td class="text-muted">${parseFloat(ing.minimum_stock)} ${ing.unit}</td>
                <td class="text-center pe-4">
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-sm btn-success" onclick="editIngredient(${ing.id})" title="Edit / Restock"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteIngredient(${ing.id})" title="Hapus"><i class="bi bi-trash"></i></button>
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

function showFormModal(ing = null) {
    if (!bsModal) {
        bsModal = new bootstrap.Modal(document.getElementById('ingredientModal'));
    }

    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    if (ing) {
        $('#modalTitle').text('Edit Bahan Baku');
        $('#ing-id').val(ing.id);
        $('#ing-name').val(ing.name);
        $('#ing-unit').val(ing.unit).trigger('input');
        $('#ing-stock').val(parseFloat(ing.stock_quantity));
        $('#ing-min').val(parseFloat(ing.minimum_stock));
    } else {
        $('#modalTitle').text('Tambah Bahan Baku');
        $('#ingredientForm')[0].reset();
        $('#ing-id').val('');
        $('.unit-label').text('-');
    }

    bsModal.show();
}

function editIngredient(id) {
    $.ajax({
        url: API_URL + '/ingredients/' + id,
        type: 'GET',
        success: function (res) {
            showFormModal(res.data);
        },
        error: function (xhr) {
            showAlert('error', 'Gagal memuat data bahan baku.');
        }
    });
}

function saveIngredient() {
    let id = $('#ing-id').val();
    let url = API_URL + '/ingredients';
    let type = 'POST';

    if (id) {
        url += '/' + id;
        type = 'PUT';
    }

    let data = {
        name: $('#ing-name').val(),
        unit: $('#ing-unit').val(),
        stock_quantity: $('#ing-stock').val(),
        minimum_stock: $('#ing-min').val()
    };

    let btn = $('#btn-save');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

    $.ajax({
        url: url,
        type: type,
        data: data,
        success: function (res) {
            bsModal.hide();
            showAlert('success', res.message);
            loadIngredients(currentPage);
        },
        error: function (xhr) {
            handleValidationErrors(xhr);
        },
        complete: function () {
            btn.prop('disabled', false).text('Simpan Bahan');
        }
    });
}

function deleteIngredient(id) {
    confirmAction('Hapus Bahan Baku', 'Apakah Anda yakin ingin menghapus bahan baku ini? (Bahan tidak bisa dihapus jika sedang dipakai di resep)', function () {
        $.ajax({
            url: API_URL + '/ingredients/' + id,
            type: 'DELETE',
            success: function (res) {
                showAlert('success', res.message);
                loadIngredients(currentPage);
            },
            error: function (xhr) {
                handleValidationErrors(xhr);
            }
        });
    });
}
