import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import useCart from '../hooks/useCart';
import useOrder from '../hooks/useOrder';
import { getOrderAvailability } from '../services/api';

const CheckoutPage = () => {
    const { items, getTotalPrice, clearCart } = useCart();
    const { submitOrder, isSubmitting, error } = useOrder();
    const navigate = useNavigate();
    const orderHistoryKey = 'koriro_order_history';
    const [orderingOpen, setOrderingOpen] = useState(true);

    React.useEffect(() => {
        getOrderAvailability()
            .then((res) => setOrderingOpen(Boolean(res.data.is_open)))
            .catch(() => setOrderingOpen(false));
    }, []);

    const [formData, setFormData] = useState({
        customer_name: '',
        table_number: '',
        notes: ''
    });

    const formatPrice = (price) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };

    const handleChange = (e) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (!orderingOpen) {
            alert('Pemesanan sedang ditutup. Silakan coba kembali nanti.');
            return;
        }

        if (items.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }

        const payload = {
            ...formData,
            items: items.map(i => ({
                product_id: i.id,
                quantity: i.quantity,
                notes: i.notes
            }))
        };

        try {
            const result = await submitOrder(payload);
            clearCart();
            const orderHistory = JSON.parse(localStorage.getItem(orderHistoryKey) || '[]');
            localStorage.setItem(
                orderHistoryKey,
                JSON.stringify([result.order_code, ...orderHistory.filter(code => code !== result.order_code)])
            );
            // Redirect ke halaman payment dengan bawa snap_token
            navigate(`/payment/${result.order_code}`, {
                state: { snapToken: result.snap_token }
            });
        } catch (err) {
            // Error sudah dihandle oleh useOrder dan ditampilkan di UI
            console.error(err);
        }
    };

    if (items.length === 0) {
        return (
            <div className="flex flex-col items-center justify-center py-20 text-center">
                <h2 className="text-2xl font-bold mb-4">Keranjang Kosong</h2>
                <button onClick={() => navigate('/menu')} className="text-brand underline">
                    Kembali ke Menu
                </button>
            </div>
        );
    }

    const subtotal = getTotalPrice();
    const tax = 0; // Rp 0 (bisa disesuaikan jika ada pajak)
    const total = subtotal + tax;

    return (
        <div className="page-enter-active max-w-2xl mx-auto">
            <h1 className="text-2xl font-bold mb-6 flex items-center gap-2">
                <button onClick={() => navigate(-1)} className="p-2 -ml-2 rounded-full hover:bg-surface text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                Checkout Pesanan
            </h1>

            {!orderingOpen && (
                <div className="mb-6 p-4 rounded-xl bg-gray-200 text-gray-800 font-semibold text-center">
                    Pemesanan sedang ditutup. Checkout tidak dapat dilanjutkan.
                </div>
            )}

            {error && (
                <div className="mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400">
                    {error}
                </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-6">
                {/* Form Data Diri */}
                <div className="bg-card p-6 rounded-2xl border border-subtle">
                    <h2 className="text-lg font-bold mb-4 border-b border-subtle pb-2">Informasi Pemesan</h2>

                    <div className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-muted mb-1">
                                Nama Lengkap <span className="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="customer_name"
                                required
                                value={formData.customer_name}
                                onChange={handleChange}
                                placeholder="Masukkan nama Anda"
                                className="w-full bg-surface border border-subtle rounded-xl px-4 py-3 text-text focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-all"
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-muted mb-1">
                                Nomor Meja <span className="text-xs font-normal opacity-70">(Opsional jika take-away)</span>
                            </label>
                            <input
                                type="text"
                                name="table_number"
                                value={formData.table_number}
                                onChange={handleChange}
                                placeholder="Misal: A3"
                                className="w-full bg-surface border border-subtle rounded-xl px-4 py-3 text-text focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-all"
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-muted mb-1">
                                Catatan Pesanan
                            </label>
                            <textarea
                                name="notes"
                                value={formData.notes}
                                onChange={handleChange}
                                placeholder="Catatan tambahan untuk pesanan ini..."
                                rows="2"
                                className="w-full bg-surface border border-subtle rounded-xl px-4 py-3 text-text focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-all"
                            ></textarea>
                        </div>
                    </div>
                </div>

                {/* Ringkasan Pesanan */}
                <div className="bg-card p-6 rounded-2xl border border-subtle">
                    <h2 className="text-lg font-bold mb-4 border-b border-subtle pb-2">Ringkasan</h2>

                    <div className="space-y-3 mb-4">
                        {items.map(item => (
                            <div key={item.id} className="flex justify-between text-sm">
                                <div>
                                    <span className="font-medium">{item.quantity}x</span> {item.name}
                                </div>
                                <span>{formatPrice(item.price * item.quantity)}</span>
                            </div>
                        ))}
                    </div>

                    <div className="border-t border-subtle pt-3 space-y-2">
                        <div className="flex justify-between text-muted text-sm">
                            <span>Subtotal</span>
                            <span>{formatPrice(subtotal)}</span>
                        </div>
                        {tax > 0 && (
                            <div className="flex justify-between text-muted text-sm">
                                <span>Pajak</span>
                                <span>{formatPrice(tax)}</span>
                            </div>
                        )}
                        <div className="flex justify-between text-lg font-bold text-brand pt-2">
                            <span>Total Pembayaran</span>
                            <span>{formatPrice(total)}</span>
                        </div>
                    </div>
                </div>

                {/* Submit */}
                <button
                    type="submit"
                    disabled={isSubmitting || !orderingOpen}
                    className={`w-full py-4 rounded-xl font-bold text-lg transition-all shadow-glow flex justify-center items-center gap-2 ${isSubmitting
                        ? 'bg-surface text-muted cursor-not-allowed'
                        : 'bg-brand hover:bg-brand-dk text-white'
                        }`}
                >
                    {isSubmitting ? (
                        <>
                            <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        </>
                    ) : (
                        'Bayar Sekarang'
                    )}
                </button>
            </form>
        </div>
    );
};

export default CheckoutPage;
