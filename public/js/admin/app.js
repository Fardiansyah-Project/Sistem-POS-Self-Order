/**
 * ═══════════════════════════════════════════════════════════════════════
 * Global Admin CMS JavaScript Helpers
 * File ini dimuat di semua halaman admin via layouts/admin.blade.php
 * ═══════════════════════════════════════════════════════════════════════
 */

// Base URL untuk semua API call admin
const API_URL = '/cms/admin/api';

// ─── Global AJAX Setup: CSRF Token + Accept JSON ───────────────────────
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        'Accept': 'application/json'
    }
});

// ─── Flash Alert Helper ────────────────────────────────────────────────
function showAlert(type, message) {
    const icons = {
        success: 'check-circle-fill',
        error: 'exclamation-triangle-fill',
        warning: 'exclamation-triangle-fill',
        info: 'info-circle-fill'
    };
    const bsType = type === 'error' ? 'danger' : type;
    const icon = icons[type] || 'info-circle-fill';

    const html = `
        <div class="alert alert-${bsType} alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-${icon} me-2"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;

    $('#alert-container').html(html);

    // Auto-dismiss setelah 5 detik
    setTimeout(function () {
        $('#alert-container .alert').alert('close');
    }, 5000);
}

// ─── Format Rupiah ─────────────────────────────────────────────────────
function formatRupiah(number) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(number);
}

// ─── Handle Validation Errors (422) ────────────────────────────────────
function handleValidationErrors(xhr) {
    // Reset semua error state
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
        const errors = xhr.responseJSON.errors;
        let firstField = null;

        $.each(errors, function (field, messages) {
            const input = $('[name="' + field + '"]');
            if (input.length) {
                input.addClass('is-invalid');
                input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
                if (!firstField) firstField = input;
            }
        });

        // Focus ke field error pertama
        if (firstField) firstField.focus();

        showAlert('error', xhr.responseJSON.message || 'Data tidak valid. Periksa kembali isian form.');
    } else if (xhr.status === 404) {
        showAlert('error', 'Data tidak ditemukan.');
    } else if (xhr.status === 500) {
        showAlert('error', 'Terjadi kesalahan pada server.');
    } else {
        const msg = (xhr.responseJSON && xhr.responseJSON.message)
            ? xhr.responseJSON.message
            : 'Terjadi kesalahan. Silakan coba lagi.';
        showAlert('error', msg);
    }
}

// ─── Loading Spinner Helpers ───────────────────────────────────────────
function showLoading(selector) {
    $(selector).html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Memuat...</span>
            </div>
            <p class="text-muted mt-2 small">Memuat data...</p>
        </div>
    `);
}

function showEmpty(selector, icon, message) {
    $(selector).html(`
        <div class="text-center py-5 text-muted">
            <i class="bi bi-${icon || 'inbox'} display-4 opacity-25 d-block mb-3"></i>
            <p>${message || 'Tidak ada data.'}</p>
        </div>
    `);
}

// ─── Konfirmasi Dialog (pakai Bootstrap Modal) ─────────────────────────
function confirmAction(title, message, callback) {
    // Hapus modal lama jika ada
    $('#modal-confirm-global').remove();

    const modal = `
        <div class="modal fade" id="modal-confirm-global" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle me-2"></i>${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">${message}</p>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-global">
                            <i class="bi bi-check-lg me-1"></i> Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

    $('body').append(modal);
    const bsModal = new bootstrap.Modal(document.getElementById('modal-confirm-global'));
    bsModal.show();

    $('#btn-confirm-global').off('click').on('click', function () {
        bsModal.hide();
        if (typeof callback === 'function') callback();
    });

    // Cleanup saat modal ditutup
    $('#modal-confirm-global').on('hidden.bs.modal', function () {
        $(this).remove();
    });
}
