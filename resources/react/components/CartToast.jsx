import React, { useEffect } from 'react';
import useCart from '../hooks/useCart';

const CartToast = () => {
    const { toast, clearToast } = useCart();

    useEffect(() => {
        if (!toast) return undefined;

        const timeoutId = window.setTimeout(clearToast, 3000);
        return () => window.clearTimeout(timeoutId);
    }, [toast, clearToast]);

    if (!toast) return null;

    return (
        <div
            key={toast.id}
            role="status"
            aria-live="polite"
            className="fixed top-20 right-4 z-[60] w-[calc(100%-2rem)] max-w-sm toast-enter"
        >
            <div className="flex items-center gap-3 rounded-xl border border-brand/40 bg-card px-4 py-3 shadow-2xl">
                <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3.25-3.25a1 1 0 111.414-1.414l2.543 2.543 6.543-6.543a1 1 0 011.414 0z" clipRule="evenodd" />
                    </svg>
                </span>
                <p className="min-w-0 text-sm text-text">
                    <span className="font-semibold">{toast.productName}</span>{' '}
                    berhasil ditambahkan ke keranjang.
                </p>
            </div>
        </div>
    );
};

export default CartToast;