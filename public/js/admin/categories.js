$(document).ready(function () {
    loadCategories();

    $('#categoryForm').submit(function (e) {
        e.preventDefault();
        saveCategory();
    });
});

let bsModal = null;

function loadCategories() {
    $.ajax({
        url: API_URL + '/categories',
        type: 'GET',
        success: function (res) {
            renderTable(res.data);
            // console.log('Response Data : ', res.data);
        },
        error: function (xhr) {
            $('#categories-tbody').html('<tr><td colspan="6" class="text-center py-4 text-danger">Gagal memuat data.</td></tr>');
            showAlert('error', 'Gagal memuat kategori.');
        }
    });
}

function renderTable(categories) {
    const tbody = $('#categories-tbody');
    tbody.empty();

    if (!categories || categories.length === 0) {
        tbody.html('<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada kategori.</td></tr>');
        return;
    }

    categories.forEach(cat => {
        let iconHtml = cat.icon ? `<i class="bi ${cat.icon} fs-5 text-muted"></i>` : '-';
        let statusHtml = cat.is_active 
            ? '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Aktif</span>' 
            : '<span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">Non-Aktif</span>';
        
        let tr = `
            <tr>
                <td class="ps-4 fw-medium text-dark">${cat.name}</td>
                <td>${iconHtml}</td>
                <td><span class="badge bg-light text-dark border">${cat.sort_order}</span></td>
                <td>${statusHtml}</td>
                <td class="text-center"><span class="badge bg-info bg-opacity-10 text-info rounded-pill">${cat.products_count || 0}</span></td>
                <td class="text-center pe-4">
                    <div class="d-flex justify-content-center gap-1">
                        <button class="btn btn-sm btn-outline-secondary" onclick="editCategory(${cat.id})" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCategory(${cat.id})" title="Hapus"><i class="bi bi-trash"></i></button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(tr);
    });
}

function showFormModal(cat = null) {
    if (!bsModal) {
        bsModal = new bootstrap.Modal(document.getElementById('categoryModal'));
    }

    // Reset error validation
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    if (cat) {
        $('#modalTitle').text('Edit Kategori');
        $('#cat-id').val(cat.id);
        $('#cat-name').val(cat.name);
        $('#cat-icon').val(cat.icon || '');
        $('#cat-sort').val(cat.sort_order);
        $('#cat-active').val(cat.is_active ? 1 : 0);
    } else {
        $('#modalTitle').text('Tambah Kategori');
        $('#categoryForm')[0].reset();
        $('#cat-id').val('');
        $('#cat-sort').val(0);
    }

    bsModal.show();
}

function editCategory(id) {
    $.ajax({
        url: API_URL + '/categories/' + id,
        type: 'GET',
        success: function (res) {
            showFormModal(res.data);
        },
        error: function (xhr) {
            showAlert('error', 'Gagal memuat data kategori.');
        }
    });
}

function saveCategory() {
    let id = $('#cat-id').val();
    let url = API_URL + '/categories';
    let type = 'POST';

    // Jika id ada, berarti Update (PUT)
    if (id) {
        url += '/' + id;
        type = 'PUT';
    }

    let data = {
        name: $('#cat-name').val(),
        icon: $('#cat-icon').val(),
        sort_order: $('#cat-sort').val(),
        is_active: $('#cat-active').val()
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
            loadCategories();
        },
        error: function (xhr) {
            handleValidationErrors(xhr);
        },
        complete: function () {
            btn.prop('disabled', false).text('Simpan Kategori');
        }
    });
}

function deleteCategory(id) {
    confirmAction('Hapus Kategori', 'Apakah Anda yakin ingin menghapus kategori ini? (Tidak bisa dihapus jika masih ada produk di dalamnya)', function () {
        $.ajax({
            url: API_URL + '/categories/' + id,
            type: 'DELETE',
            success: function (res) {
                showAlert('success', res.message);
                loadCategories();
            },
            error: function (xhr) {
                handleValidationErrors(xhr);
            }
        });
    });
}
