$(document).ready(function () {
    loadCategoriesDropdown().then(() => {
        if (FORM_MODE === 'edit') {
            loadProductData();
        } else {
            // mode create
            $('#product-form').removeClass('d-none');
        }
    });

    $('#product-form').submit(function (e) {
        e.preventDefault();
        saveProduct();
    });
});

function loadCategoriesDropdown() {
    return $.ajax({
        url: API_URL + '/categories',
        type: 'GET',
        success: function (res) {
            let select = $('#category-select');
            select.empty();
            select.append('<option value="">-- Pilih Kategori --</option>');
            res.data.forEach(cat => {
                select.append(`<option value="${cat.id}">${cat.name}</option>`);
            });
        }
    });
}

function loadProductData() {
    $.ajax({
        url: API_URL + '/products/' + PRODUCT_ID,
        type: 'GET',
        success: function (res) {
            let p = res.data;
            $('input[name="name"]').val(p.name);
            $('#category-select').val(p.category_id);
            $('textarea[name="description"]').val(p.description);
            $('input[name="price"]').val(parseFloat(p.price));
            $('select[name="is_available"]').val(p.is_available ? 1 : 0);
            $('input[name="sort_order"]').val(p.sort_order);

            if (p.image_url) {
                $('#current-image-preview img').attr('src', p.image_url);
                $('#current-image-preview').removeClass('d-none');
            }

            $('#loading-indicator').addClass('d-none');
            $('#product-form').removeClass('d-none');
        },
        error: function () {
            showAlert('error', 'Gagal memuat data produk.');
            $('#loading-indicator').html('<span class="text-danger">Data produk tidak ditemukan.</span>');
        }
    });
}

function saveProduct() {
    let form = $('#product-form')[0];
    let formData = new FormData(form);

    let url = API_URL + '/products';
    if (FORM_MODE === 'edit') {
        url += '/' + PRODUCT_ID;
        // In Laravel, file uploads via PUT/PATCH need to be sent via POST with _method=PUT
        // But in our controller, we mapped update to POST explicitly for this reason
    }

    let btn = $('#btn-save');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Menyimpan...');

    $.ajax({
        url: url,
        type: 'POST', // We use POST for both create and update (file upload quirk)
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            showAlert('success', res.message);
            setTimeout(() => {
                window.location.href = '/cms/admin/products';
            }, 1000);
        },
        error: function (xhr) {
            handleValidationErrors(xhr);
            btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Simpan Produk');
        }
    });
}
