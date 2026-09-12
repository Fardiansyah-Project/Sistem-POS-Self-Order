import { useState, useCallback, useRef } from 'react';
import { createOrder, getOrderStatus } from '../services/api';

/**
 * useOrder — Mengelola pembuatan pesanan dan polling status.
 */
const useOrder = () => {
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [orderResult, setOrderResult]   = useState(null); // { order_code, snap_token, total }
    const [orderStatus, setOrderStatus]   = useState(null);
    const [error, setError]               = useState(null);
    const pollingRef                      = useRef(null);

    /**
     * Submit pesanan ke API Laravel.
     * @param {Object} orderData - { customer_name, table_number, notes, items }
     * @returns {Object}         - { order_code, snap_token, total }
     */
    const submitOrder = useCallback(async (orderData) => {
        setIsSubmitting(true);
        setError(null);

        try {
            const res = await createOrder(orderData);
            setOrderResult(res.data);
            return res.data;
        } catch (err) {
            const message = err.response?.data?.message || 'Gagal membuat pesanan. Coba lagi.';
            setError(message);
            throw new Error(message);
        } finally {
            setIsSubmitting(false);
        }
    }, []);

    /**
     * Mulai polling status pesanan setiap 3 detik.
     * Berhenti otomatis ketika payment_status = paid/failed/cancelled/expired.
     *
     * @param {string}   orderCode  - Kode pesanan
     * @param {Function} onPaid     - Callback ketika pembayaran berhasil
     */
    const startPolling = useCallback((orderCode, onPaid) => {
        stopPolling();

        pollingRef.current = setInterval(async () => {
            try {
                const res    = await getOrderStatus(orderCode);
                const status = res.data.data;
                setOrderStatus(status);

                const terminalStatuses = ['paid', 'failed', 'cancelled', 'expired'];
                if (terminalStatuses.includes(status.payment_status)) {
                    stopPolling();
                    if (status.payment_status === 'paid' && onPaid) {
                        onPaid(status);
                    }
                }
            } catch {
                // Abaikan error polling — coba lagi di interval berikutnya
            }
        }, 3000);
    }, []);

    /** Hentikan polling */
    const stopPolling = useCallback(() => {
        if (pollingRef.current) {
            clearInterval(pollingRef.current);
            pollingRef.current = null;
        }
    }, []);

    return {
        isSubmitting,
        orderResult,
        orderStatus,
        error,
        submitOrder,
        startPolling,
        stopPolling,
    };
};

export default useOrder;
