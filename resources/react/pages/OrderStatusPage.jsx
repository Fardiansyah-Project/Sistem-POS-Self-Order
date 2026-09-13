import React, { useEffect, useState } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import useOrder from '../hooks/useOrder';
import { createPaymentToken } from '../services/api';

const OrderStatusPage = () => {
    const { orderCode } = useParams();
    const navigate = useNavigate();
    const { orderStatus, startPolling, stopPolling } = useOrder();
    const [isPaying, setIsPaying] = useState(false);
    const [paymentError, setPaymentError] = useState(null);

    useEffect(() => {
        // Mulai polling status tiap 3 detik
        startPolling(orderCode);
        
        return () => {
            stopPolling();
        };
    }, [orderCode, startPolling, stopPolling]);

    const formatPrice = (price) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };

    if (!orderStatus) {
        return (
            <div className="flex justify-center py-20">
                <div className="w-12 h-12 border-4 border-brand border-t-transparent rounded-full animate-spin"></div>
            </div>
        );
    }

    const { payment_status, order_status, items, total_amount, customer_name, table_number } = orderStatus;

    // Mapping Status Text
    const getPaymentBadge = () => {
        switch (payment_status) {
            case 'paid': return <span className="px-3 py-1 bg-green-500/20 text-green-400 border border-green-500/30 rounded-full text-xs font-bold uppercase tracking-wider">LUNAS</span>;
            case 'pending': return <span className="px-3 py-1 bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 rounded-full text-xs font-bold uppercase tracking-wider">MENUNGGU PEMBAYARAN</span>;
            case 'failed': return <span className="px-3 py-1 bg-red-500/20 text-red-400 border border-red-500/30 rounded-full text-xs font-bold uppercase tracking-wider">GAGAL</span>;
            default: return <span className="px-3 py-1 bg-surface text-muted border border-subtle rounded-full text-xs font-bold uppercase tracking-wider">{payment_status}</span>;
        }
    };

    const getOrderProgress = () => {
        const steps = [
            { key: 'waiting', label: 'Menunggu' },
            { key: 'processing', label: 'Diproses' },
            { key: 'ready', label: 'Siap Diambil' },
            { key: 'completed', label: 'Selesai' }
        ];

        let currentIndex = steps.findIndex(s => s.key === order_status);
        if (currentIndex === -1) currentIndex = 0;

        return (
            <div className="flex justify-between relative mt-8 mb-4">
                <div className="absolute top-1/2 left-0 right-0 h-1 bg-surface -z-10 -translate-y-1/2 rounded-full"></div>
                <div 
                    className="absolute top-1/2 left-0 h-1 bg-brand -z-10 -translate-y-1/2 rounded-full transition-all duration-500"
                    style={{ width: `${(currentIndex / (steps.length - 1)) * 100}%` }}
                ></div>

                {steps.map((step, index) => {
                    const isActive = index <= currentIndex;
                    const isCurrent = index === currentIndex;
                    return (
                        <div key={step.key} className="flex flex-col items-center gap-2">
                            <div className={`w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm transition-colors duration-300 ${
                                isActive ? 'bg-brand text-white shadow-glow' : 'bg-surface border-2 border-subtle text-muted'
                            }`}>
                                {index + 1}
                            </div>
                            <span className={`text-xs font-medium ${isCurrent ? 'text-brand' : (isActive ? 'text-text' : 'text-muted')}`}>
                                {step.label}
                            </span>
                        </div>
                    );
                })}
            </div>
        );
    };

    const handlePayOrder = async () => {
        setIsPaying(true);
        setPaymentError(null);

        try {
            const response = await createPaymentToken(orderCode);
            navigate(`/payment/${orderCode}`, {
                state: { snapToken: response.data.snap_token },
            });
        } catch (error) {
            setPaymentError(error.response?.data?.message || 'Gagal membuka pembayaran. Coba lagi.');
        } finally {
            setIsPaying(false);
        }
    };

    return (
        <div className="page-enter-active max-w-2xl mx-auto pb-10">
            {/* Kartu Status Utama */}
            <div className="bg-card rounded-3xl p-6 md:p-8 border border-subtle shadow-2xl relative overflow-hidden text-center mb-6">
                <div className="absolute top-0 left-0 right-0 h-2 bg-brand"></div>
                
                <h1 className="text-xl font-medium text-muted mb-1">Status Pesanan Anda</h1>
                <div className="text-3xl font-bold font-mono mb-4 text-white">
                    {orderCode}
                </div>
                
                <div className="mb-6">
                    {getPaymentBadge()}
                </div>

                {/* Progress Bar (Hanya tampil jika sudah lunas atau processing) */}
                {(payment_status === 'paid' || order_status !== 'waiting') && (
                    <div className="border-t border-subtle pt-4 mt-2">
                        {getOrderProgress()}
                    </div>
                )}
            </div>

            {/* Detail Pesanan */}
            <div className="bg-card rounded-2xl p-6 border border-subtle mb-6">
                <h2 className="text-lg font-bold border-b border-subtle pb-3 mb-4">Detail Pemesan</h2>
                <div className="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <span className="block text-muted mb-1">Nama</span>
                        <span className="font-medium text-text">{customer_name}</span>
                    </div>
                    <div>
                        <span className="block text-muted mb-1">Meja</span>
                        <span className="font-medium text-text">{table_number || 'Take-away'}</span>
                    </div>
                </div>
                
                <h2 className="text-lg font-bold border-b border-subtle pb-3 mb-4 mt-6">Item Pesanan</h2>
                <div className="space-y-3 mb-4">
                    {items?.map(item => (
                        <div key={item.id} className="flex justify-between text-sm">
                            <div>
                                <span className="font-medium text-text">{item.quantity}x</span>{' '}
                                <span className="text-muted">{item.product_name}</span>
                            </div>
                            <span className="font-medium text-text">{formatPrice(item.subtotal)}</span>
                        </div>
                    ))}
                </div>
                <div className="flex justify-between text-lg font-bold text-brand pt-4 border-t border-subtle">
                    <span>Total</span>
                    <span>{formatPrice(total_amount)}</span>
                </div>
            </div>

            <div className="text-center">
                {payment_status === 'pending' && (
                    <>
                        {paymentError && (
                            <p className="text-sm text-red-400 mb-3">{paymentError}</p>
                        )}
                        <button
                            type="button"
                            onClick={handlePayOrder}
                            disabled={isPaying}
                            className="w-full sm:w-auto inline-block py-3 px-6 bg-brand rounded-xl text-white font-medium hover:bg-brand-dk transition-colors disabled:opacity-60 disabled:cursor-not-allowed mb-3"
                        >
                            {isPaying ? 'Membuka Pembayaran...' : 'Bayar Pesanan'}
                        </button>

                    </>
                )}
                <Link to="/menu" className="inline-block py-3 px-6 bg-surface border border-subtle rounded-xl text-white font-medium hover:bg-card transition-colors">
                    Pesan Lagi
                </Link>
            </div>
        </div>
    );
};

export default OrderStatusPage;
