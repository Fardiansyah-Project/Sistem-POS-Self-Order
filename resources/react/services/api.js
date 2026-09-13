import axios from 'axios';

// Base URL dari environment variable Vite
const api = axios.create({
    baseURL: import.meta.env.VITE_API_BASE_URL || '/api/v1',
    headers: {
        'Content-Type': 'application/json',
        'Accept':       'application/json',
    },
    timeout: 15000,
});

// Interceptor: tambah Authorization token jika ada
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Interceptor: handle error global
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401) {
            // Token expired atau tidak valid — hapus dan redirect
            localStorage.removeItem('auth_token');
        }
        return Promise.reject(error);
    }
);

// ─── API Functions ───────────────────────────────────────────────

/** Ambil semua kategori aktif */
export const getCategories = () => api.get('/categories');

/** Ambil semua produk, dengan filter kategori opsional */
export const getProducts = (categorySlug = null) =>
    api.get('/products', { params: categorySlug ? { category: categorySlug } : {} });

/**
 * Buat pesanan baru
 * @param {Object} orderData - { customer_name, table_number, notes, items: [{product_id, quantity, notes}] }
 */
export const createOrder = (orderData) => api.post('/orders', orderData);

/** Buat token pembayaran baru untuk pesanan yang masih pending */
export const createPaymentToken = (orderCode) =>
    api.post(`/orders/${orderCode}/payment-token`);

/**
 * Cek status pesanan (untuk polling)
 * @param {string} orderCode - Kode pesanan, e.g. KRC-20240912-0001
 */
export const getOrderStatus = (orderCode) => api.get(`/orders/${orderCode}/status`);

export default api;
