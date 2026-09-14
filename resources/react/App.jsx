import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import MenuPage from './pages/MenuPage';
import CheckoutPage from './pages/CheckoutPage';
import PaymentPage from './pages/PaymentPage';
import OrderStatusPage from './pages/OrderStatusPage';
import OrdersPage from './pages/OrdersPage';
import Navbar from './components/Navbar';
import CartDrawer from './components/CartDrawer';
import CartToast from './components/CartToast';

const App = () => {
    return (
        <div className="flex flex-col min-h-screen bg-bg text-text">
            {/* Header Global */}
            <Navbar />

            {/* Main Content (Routes) */}
            <main className="flex-grow pb-24 pt-20 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">
                <Routes>
                    <Route path="/" element={<Navigate to="/menu" replace />} />
                    <Route path="/menu" element={<MenuPage />} />
                    <Route path="/checkout" element={<CheckoutPage />} />
                    <Route path="/payment/:orderCode" element={<PaymentPage />} />
                    <Route path="/order/:orderCode" element={<OrderStatusPage />} />
                    <Route path="/orders" element={<OrdersPage />} />
                    <Route path="*" element={<Navigate to="/menu" replace />} />
                </Routes>
            </main>

            {/* Cart Drawer Overlay */}
            <CartDrawer />
            <CartToast />
        </div>
    );
};

export default App;
