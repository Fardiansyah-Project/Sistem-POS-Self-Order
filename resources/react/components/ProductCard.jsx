import React from 'react';
import useCart from '../hooks/useCart';

const ProductCard = ({ product }) => {
    const { addItem } = useCart();

    const formatPrice = (price) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
        }).format(price);
    };

    return (
        <div className="bg-card rounded-2xl overflow-hidden border border-subtle flex flex-col h-full transition-transform hover:-translate-y-1 hover:shadow-lg">
            <div className="relative pt-[100%] bg-surface">
                <img
                    src={product.image_url}
                    alt={product.name}
                    className="absolute inset-0 w-full h-full object-cover"
                    loading="lazy"
                />
            </div>
            <div className="p-4 flex flex-col flex-grow">
                <div className="flex justify-between items-start gap-2 mb-2">
                    <h3 className="font-semibold text-text leading-tight">{product.name}</h3>
                    <span className="font-bold text-brand whitespace-nowrap">
                        {formatPrice(product.price)}
                    </span>
                </div>
                <p className="text-sm text-muted mb-4 flex-grow line-clamp-2">
                    {product.description}
                </p>
                <button
                    onClick={() => addItem(product)}
                    className="w-full py-2.5 px-4 bg-brand hover:bg-brand-dk text-white rounded-xl font-medium transition-colors flex items-center justify-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd" />
                    </svg>
                    Tambah
                </button>
            </div>
        </div>
    );
};

export default ProductCard;
