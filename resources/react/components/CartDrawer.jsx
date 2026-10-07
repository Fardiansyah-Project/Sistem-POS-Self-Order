import React from 'react';
import { useNavigate } from 'react-router-dom';
import useCart from '../hooks/useCart';
import CartItem from './CartItem';

const CartDrawer = () => {

    const { isOpen, closeCart, items, getTotalPrice, orderingOpen } = useCart();
    const navigate = useNavigate();

    const formatPrice = (price) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };

    const handleCheckout = () => {
        closeCart();
        navigate('/checkout');
    };

    if (!isOpen) return null;

    return (
        <>
            {/* Backdrop */}
            <div
                className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 transition-opacity"
                onClick={closeCart}
            />

            {/* Drawer */}
            <div className="fixed inset-y-0 right-0 max-w-md w-full bg-surface shadow-2xl z-50 flex flex-col transform transition-transform border-l border-subtle">
                {/* Header */}
                <div className="flex items-center justify-between p-4 border-b border-subtle bg-card">
                    <h2 className="text-lg font-bold text-text">Keranjang Pesanan</h2>
                    <button
                        onClick={closeCart}
                        className="p-2 text-muted hover:text-white rounded-full hover:bg-surface transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {/* Items */}
                <div className="flex-grow overflow-y-auto custom-scrollbar">
                    {items.length === 0 ? (
                        <div className="flex flex-col items-center justify-center h-full text-muted p-8 text-center gap-4">
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-16 w-16 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <p>Keranjang masih kosong.<br/>Yuk, pesan kopi favoritmu!</p>
                            <button
                                onClick={closeCart}
                                className="px-6 py-2 mt-2 bg-card border border-subtle rounded-full text-white font-medium hover:bg-brand transition-colors"
                            >
                                Lihat Menu
                            </button>
                        </div>
                    ) : (
                        <div className="flex flex-col">
                            {items.map((item) => (
                                <CartItem key={item.id} item={item} />
                            ))}
                        </div>
                    )}
                </div>

                {/* Footer / Checkout Button */}
                {items.length > 0 && (
                    <div className="p-4 bg-card border-t border-subtle">
                        <div className="flex justify-between items-center mb-4">
                            <span className="text-muted font-medium">Total Harga</span>
                            <span className="text-xl font-bold text-brand">
                                {formatPrice(getTotalPrice())}
                            </span>
                        </div>
                        {!orderingOpen && <p className="mb-3 text-center text-sm font-medium text-gray-400">Pemesanan sedang ditutup.</p>}
                        <button
                            onClick={handleCheckout}

                            disabled={!orderingOpen}
                            className="w-full py-3.5 bg-brand hover:bg-brand-dk text-white rounded-xl font-bold text-lg transition-colors shadow-glow flex items-center justify-center gap-2 disabled:bg-gray-500 disabled:cursor-not-allowed"
                        >
                            Lanjut ke Pembayaran
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clipRule="evenodd" />
                            </svg>
                        </button>
                    </div>
                )}
            </div>
        </>
    );
};

export default CartDrawer;
