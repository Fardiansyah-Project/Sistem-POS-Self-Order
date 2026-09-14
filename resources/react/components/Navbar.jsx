import React from "react";
import { Link } from "react-router-dom";
import useCart from "../hooks/useCart";
import { FaShoppingBag, FaUser } from "react-icons/fa";
import { FaTasks } from "react-icons/fa";
import { FaHome } from "react-icons/fa";

const Navbar = () => {
    const { getTotalItems, toggleCart } = useCart();
    const totalItems = getTotalItems();

    return (
        <nav className="fixed top-0 left-0 right-0 z-40 glass border-b border-subtle">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between items-center h-16">
                    {/* Brand */}
                    <Link to="/menu" className="flex items-center gap-2">
                        <div className="w-8 h-8 rounded-full bg-brand flex items-center justify-center font-bold text-white shadow-glow">
                            K
                        </div>
                        <span className="font-bold text-xl tracking-tight gradient-text">
                            Koriru
                        </span>
                    </Link>

                    {/* Navigation Links */}
                    <div className="flex items-center gap-3">
                        {/* <Link
                            to="/menu"
                            className="text-muted hover:text-brand transition-colors"
                        >
                            <FaHome className="h-6 w-6" />
                        </Link> */}

                        <Link
                            to="/orders"
                            className="flex items-center gap-2 pl-3 pr-4 py-2 rounded-full border border-subtle bg-surface text-text text-sm font-medium hover:border-brand/40 transition-colors"
                        >
                            <FaTasks className="h-4 w-4" />
                            <span>Pesanan</span>
                        </Link>

                        <button
                            onClick={toggleCart}
                            className="relative bg-white flex items-center gap-2 pl-3 pr-4 py-2 rounded-full text-slate-900 text-sm font-medium hover:bg-white/90 transition-colors"
                        >
                            <FaShoppingBag className="h-4 w-4" />
                            <span>Keranjang</span>
                            {totalItems > 0 && (
                                <span className="absolute top-0 right-0 flex h-6 w-6 -translate-y-1/2 translate-x-1/2">
                                    <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span className="relative inline-flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold">
                                        {totalItems}
                                    </span>
                                </span>
                            )}
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    );
};

export default Navbar;
