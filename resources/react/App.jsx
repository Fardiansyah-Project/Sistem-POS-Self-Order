import React from 'react';
import Navbar from './components/Navbar';
import CartDrawer from './components/CartDrawer';
import CartToast from './components/CartToast';
import RoutesNavigate from './RoutesNavigate';

const App = () => {
    return (
        <div className="flex flex-col min-h-screen bg-bg text-text">
            <Navbar />
            <main className="flex-grow pb-24 pt-20 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8">
                <RoutesNavigate />
            </main>
            <CartDrawer />
            <CartToast />
        </div>
    );
};

export default App;
