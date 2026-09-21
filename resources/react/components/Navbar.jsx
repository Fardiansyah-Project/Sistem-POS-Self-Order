import React, { useState } from "react";
import { Link } from "react-router-dom";
import useCart from "../hooks/useCart";
import { FaShoppingBag, FaTasks, FaBars, FaTimes } from "react-icons/fa";

const Navbar = () => {
    const { getTotalItems, toggleCart } = useCart();
    const totalItems = getTotalItems();
    const [mobileOpen, setMobileOpen] = useState(false);

    return (
        <nav className="fixed top-0 left-0 right-0 z-40 glass border-b border-subtle">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between items-center h-16">
                    {/* Brand */}
                    <Link
                        to="/menu"
                        className="flex items-center gap-2 shrink-0"
                        onClick={() => setMobileOpen(false)}
                    >
                        <div className="w-8 h-8 rounded-full bg-brand flex items-center justify-center font-bold text-white shadow-glow shrink-0">
                            K
                        </div>
                        <span className="font-bold text-xl tracking-tight gradient-text">
                            Koriru
                        </span>
                    </Link>

                    {/* Desktop nav */}
                    <div className="hidden md:flex items-center gap-3">
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

                    {/* Mobile controls: cart icon + hamburger */}
                    <div className="flex md:hidden items-center gap-2">
                        <button
                            onClick={toggleCart}
                            aria-label="Buka keranjang"
                            className="relative bg-white flex items-center justify-center w-10 h-10 rounded-full text-slate-900 hover:bg-white/90 transition-colors shrink-0"
                        >
                            <FaShoppingBag className="h-4 w-4" />
                            {totalItems > 0 && (
                                <span className="absolute top-0 right-0 flex h-5 w-5 -translate-y-1/3 translate-x-1/3">
                                    <span className="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span className="relative inline-flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold">
                                        {totalItems}
                                    </span>
                                </span>
                            )}
                        </button>

                        <button
                            onClick={() => setMobileOpen((v) => !v)}
                            aria-label="Buka menu"
                            aria-expanded={mobileOpen}
                            className="flex items-center justify-center w-10 h-10 rounded-full border border-subtle bg-surface text-text shrink-0"
                        >
                            {mobileOpen ? (
                                <FaTimes className="h-4 w-4" />
                            ) : (
                                <FaBars className="h-4 w-4" />
                            )}
                        </button>
                    </div>
                </div>
            </div>

            {/* Mobile dropdown panel */}
            <div
                className={`md:hidden overflow-hidden transition-all duration-200 ease-in-out border-t border-subtle ${
                    mobileOpen
                        ? "max-h-40 opacity-100"
                        : "max-h-0 opacity-0 border-t-0"
                }`}
            >
                <div className="px-4 sm:px-6 py-3 flex flex-col gap-2">
                    <Link
                        to="/orders"
                        onClick={() => setMobileOpen(false)}
                        className="flex items-center gap-2 px-4 py-3 rounded-xl border border-subtle bg-surface text-text text-sm font-medium"
                    >
                        <FaTasks className="h-4 w-4" />
                        <span>Pesanan</span>
                    </Link>
                </div>
            </div>
        </nav>
    );
};

export default Navbar;
