import React from 'react';
import useCart from '../hooks/useCart';

const CartItem = ({ item }) => {
    const { addItem, decrementItem, updateNotes } = useCart();

    const formatPrice = (price) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };

    return (
        <div className="flex gap-4 p-4 border-b border-subtle bg-surface/50">
            <img
                src={item.image_url}
                alt={item.name}
                className="w-20 h-20 object-cover rounded-xl bg-card"
            />
            <div className="flex-grow flex flex-col justify-between">
                <div>
                    <h4 className="font-medium text-text text-sm sm:text-base leading-tight mb-1">
                        {item.name}
                    </h4>
                    <div className="text-brand font-bold text-sm">
                        {formatPrice(item.price)}
                    </div>
                </div>

                <div className="flex items-center justify-between mt-2">
                    {/* Quantity Controls */}
                    <div className="flex items-center bg-card rounded-lg border border-subtle">
                        <button
                            onClick={() => decrementItem(item.id)}
                            className="w-8 h-8 flex items-center justify-center text-muted hover:text-white transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clipRule="evenodd" />
                            </svg>
                        </button>
                        <span className="w-8 text-center text-sm font-medium">
                            {item.quantity}
                        </span>
                        <button
                            onClick={() => addItem(item)}
                            className="w-8 h-8 flex items-center justify-center text-muted hover:text-white transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" className="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default CartItem;
