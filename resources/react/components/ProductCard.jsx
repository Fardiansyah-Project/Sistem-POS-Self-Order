import React from "react";
import useCart from "../hooks/useCart";

const ProductCard = ({ product, orderingOpen = true }) => {
    const { addItem } = useCart();

    const formatPrice = (price) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(price);
    };

    return (
        <div className={`bg-card rounded-2xl overflow-hidden border border-subtle flex flex-col h-full transition-all ${orderingOpen ? 'hover:-translate-y-1 hover:shadow-lg' : 'grayscale opacity-60'}`}>
            <div className="relative pt-[100%] bg-surface">
                <img
                    src={product.image_url}
                    alt={product.name}
                    className="absolute inset-0 w-full h-full object-cover"
                    loading="lazy"
                />
            </div>
            <div className="p-3 sm:p-4 flex flex-col flex-grow">
                <div className="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-0.5 sm:gap-2 mb-1.5 sm:mb-2">
                    <h3 className="font-semibold text-text text-sm sm:text-base leading-tight line-clamp-2">
                        {product.name}
                    </h3>
                    <span className="font-bold text-brand text-xs sm:text-sm whitespace-nowrap">
                        {formatPrice(product.price)}
                    </span>
                </div>
                <p className="text-xs sm:text-sm text-muted mb-3 sm:mb-4 flex-grow line-clamp-2">
                    {product.description}
                </p>
                <button
                    onClick={() => addItem(product)}
                    disabled={!orderingOpen}
                    className="w-full py-2 sm:py-2.5 px-3 sm:px-4 bg-brand hover:bg-brand-dk text-white rounded-xl font-medium text-sm sm:text-base transition-colors flex items-center justify-center gap-1.5 sm:gap-2 disabled:bg-gray-500 disabled:cursor-not-allowed"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        className="h-4 w-4 sm:h-5 sm:w-5 shrink-0"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fillRule="evenodd"
                            d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                            clipRule="evenodd"
                        />
                    </svg>
                    {orderingOpen ? 'Tambah' : 'Order Ditutup'}
                </button>
            </div>
        </div>
    );
};

export default ProductCard;
