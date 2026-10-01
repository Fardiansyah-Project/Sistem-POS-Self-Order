$(document).ready(function () {
    loadProductInfo();
    loadIngredientsDropdown();
    loadRecipes();

    // Update label satuan saat bahan baku dipilih
    $('#ingredient-select').on('change', function() {
        let selected = $(this).find('option:selected');
        let unit = selected.data('unit') || '-';
        $('#unit-label').text(unit);
    });

    $('#recipe-form').submit(function (e) {
        e.preventDefault();
        saveRecipe();
    });
});

function loadProductInfo() {
    $.ajax({
        url: API_URL + '/products/' + PRODUCT_ID,
        type: 'GET',
        success: function (res) {
            let prod = res.data;
            let imageHtml = prod.image_url 
                ? `<img src="${prod.image_url}" alt="${prod.name}" class="rounded shadow-sm mb-3" style="width: 120px; height: 120px; object-fit: cover;">`
                : `<div class="bg-light rounded mx-auto d-flex align-items-center justify-content-center text-muted mb-3" style="width: 120px; height: 120px;"><i class="bi bi-image display-4"></i></div>`;
            
            let categoryHtml = prod.category ? prod.category.name : 'Tanpa Kategori';

            let html = `
                ${imageHtml}
                <h5 class="fw-bold mb-1">${prod.name}</h5>
                <span class="badge bg-secondary rounded-pill mb-3">${categoryHtml}</span>
                <p class="text-muted small mb-3">${prod.description || 'Tidak ada deskripsi.'}</p>
                <div class="fw-bold fs-5 text-brand" style="color: #c97d20;">${formatRupiah(prod.price)}</div>
            `;
            $('#product-info').html(html);
        },
        error: function () {
            $('#product-info').html('<div class="text-danger">Gagal memuat info produk.</div>');
        }
    });
}

function loadIngredientsDropdown() {
    $.ajax({
        url: API_URL + '/ingredients?all=true',
        type: 'GET',
        success: function (res) {
            let select = $('#ingredient-select');
            select.empty();
            select.append('<option value="">-- Pilih Bahan Baku --</option>');
            res.data.forEach(ing => {
                select.append(`<option value="${ing.id}" data-unit="${ing.unit}">${ing.name}</option>`);
            });
        }
    });
}

function loadRecipes() {
    $.ajax({
        url: API_URL + '/products/' + PRODUCT_ID + '/recipes',
        type: 'GET',
        success: function (res) {
            renderTable(res.data.recipes);
        },
        error: function () {
            $('#recipes-tbody').html('<tr><td colspan="3" class="text-center py-4 text-danger">Gagal memuat resep.</td></tr>');
        }
    });
}

function renderTable(recipes) {
    const tbody = $('#recipes-tbody');
    tbody.empty();

    if (!recipes || recipes.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="3" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox d-block fs-2 mb-2 opacity-50"></i>
                    Produk ini belum memiliki komposisi bahan baku.
                </td>
            </tr>
        `);
        return;
    }

    recipes.forEach(ing => {
        let tr = `
            <tr>
                <td class="ps-4 fw-medium">${ing.name}</td>
                <td>
                    <span class="fw-bold">${parseFloat(ing.pivot.quantity_needed)}</span> 
                    <span class="text-muted">${ing.unit}</span>
                </td>
                <td class="text-center pe-4">
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteRecipe(${ing.id})" title="Hapus dari Resep"><i class="bi bi-x-lg"></i></button>
                </td>
            </tr>
        `;
        tbody.append(tr);
    });
}

function saveRecipe() {
    let btn = $('#btn-add-recipe');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    let data = {
        ingredient_id: $('#ingredient-select').val(),
        quantity_needed: $('#quantity-input').val()
    };

    $.ajax({
        url: API_URL + '/products/' + PRODUCT_ID + '/recipes',
        type: 'POST',
        data: data,
        success: function (res) {
            showAlert('success', res.message);
            $('#quantity-input').val(''); // reset
            loadRecipes(); // reload table
        },
        error: function (xhr) {
            handleValidationErrors(xhr);
        },
        complete: function () {
            btn.prop('disabled', false).html('<i class="bi bi-plus-lg"></i> Tambah');
        }
    });
}

function deleteRecipe(ingredientId) {
    confirmAction('Hapus Resep', 'Hapus bahan ini dari komposisi resep?', function () {
        $.ajax({
            url: API_URL + '/products/' + PRODUCT_ID + '/recipes/' + ingredientId,
            type: 'DELETE',
            success: function (res) {
                showAlert('success', res.message);
                loadRecipes();
            },
            error: function (xhr) {
                showAlert('error', 'Gagal menghapus bahan dari resep.');
            }
        });
    });
}
