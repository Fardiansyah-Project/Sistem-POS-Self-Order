import React from "react";
import { Link } from "react-router-dom";
import useCart from "../hooks/useCart";
import { FaShoppingBag } from "react-icons/fa";
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
                    <div className="flex items-center gap-4">
                    <Link to="/menu">
                       <FaHome className="h-6 w-6"/>
                    </Link>
                        <Link
                            to="/orders"
                            className="text-sm text-muted hover:text-brand transition-colors"
                        >
                            <FaTasks className="h-6 w-6" />
                        </Link>
                        <button
                            onClick={toggleCart}
                            className="relative p-2 text-text hover:text-brand transition-colors rounded-full hover:bg-surface"
                        >
                            {/* <svg
                                xmlns="http://www.w3.org/2000/svg"
                                className="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                />
                            </svg> */}
                            <FaShoppingBag className="h-6 w-6" />
                            {totalItems > 0 && (
                                <span className="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-500 rounded-full">
                                    {totalItems}
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
