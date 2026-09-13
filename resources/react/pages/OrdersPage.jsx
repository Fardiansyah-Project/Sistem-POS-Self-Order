import React, { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { getOrderStatus } from '../services/api';

const ORDER_HISTORY_KEY = 'koriro_order_history';

const OrdersPage = () => {
    const [orders, setOrders] = useState([]);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        const orderCodes = JSON.parse(localStorage.getItem(ORDER_HISTORY_KEY) || '[]');

        const loadOrders = async () => {
            const results = await Promise.all(
                orderCodes.map(async (orderCode) => {
                    try {
                        const response = await getOrderStatus(orderCode);
                        return response.data.data;
                    } catch {
                        return { order_code: orderCode, unavailable: true };
                    }
                })
            );

            setOrders(results);
            setIsLoading(false);
        };

        loadOrders();
    }, []);

    const formatPrice = (price) => new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);

    const getPaymentLabel = (status) => ({
        paid: 'Lunas',
        pending: 'Menunggu Pembayaran',
        failed: 'Gagal',
        cancelled: 'Dibatalkan',
        expired: 'Kadaluarsa',
    }[status] || status);

    return (
        <div className="page-enter-active max-w-3xl mx-auto pb-10">
            <div className="flex items-center justify-between mb-6">
                <div>
                    <h1 className="text-2xl font-bold text-white">Pesanan Saya</h1>
                    <p className="text-sm text-muted mt-1">Lihat status dan lanjutkan pembayaran pesanan.</p>
                </div>
                <Link to="/menu" className="py-2 px-4 bg-surface border border-subtle rounded-xl text-white text-sm">
                    Pesan Lagi
                </Link>
            </div>

            {isLoading ? (
                <div className="flex justify-center py-20">
                    <div className="w-10 h-10 border-4 border-brand border-t-transparent rounded-full animate-spin"></div>
                </div>
            ) : orders.length === 0 ? (
                <div className="bg-card border border-subtle rounded-2xl p-10 text-center">
                    <p className="text-muted mb-5">Belum ada riwayat pesanan.</p>
                    <Link to="/menu" className="inline-block py-3 px-6 bg-brand rounded-xl text-white font-medium">
                        Lihat Menu
                    </Link>
                </div>
            ) : (
                <div className="space-y-4">
                    {orders.map((order) => (
                        <Link
                            key={order.order_code}
                            to={`/order/${order.order_code}`}
                            className="block bg-card border border-subtle rounded-2xl p-5 hover:border-brand transition-colors"
                        >
                            <div className="flex items-start justify-between gap-4">
                                <div>
                                    <p className="font-mono font-bold text-white">{order.order_code}</p>
                                    {order.unavailable ? (
                                        <p className="text-sm text-muted mt-2">Detail pesanan tidak tersedia.</p>
                                    ) : (
                                        <p className="text-sm text-muted mt-2">
                                            {order.customer_name} · {formatPrice(order.total_amount)}
                                        </p>
                                    )}
                                </div>
                                {!order.unavailable && (
                                    <span className={`text-xs font-bold uppercase tracking-wide ${order.payment_status === 'paid' ? 'text-green-400' : 'text-brand'}`}>
                                        {getPaymentLabel(order.payment_status)}
                                    </span>
                                )}
                            </div>
                        </Link>
                    ))}
                </div>
            )}
        </div>
    );
};

export default OrdersPage;
