import React from 'react';
import useProducts from '../hooks/useProducts';
import CategoryTabs from '../components/CategoryTabs';
import ProductCard from '../components/ProductCard';
import '../index.css';
import bgHero from '../../assets/hero-bg.jpg';

const MenuPage = () => {
    const {
        categories,
        products,
        activeCategory,
        setActiveCategory,
        isLoading,
        error
    } = useProducts();

    return (
        <div className="page-enter-active">
            {/* Header Banner */}
            <div
                className="bg-hero flex items-center mb-8 min-h-[300px] sm:min-h-[320px] p-6 sm:p-12 rounded-2xl border border-transparent overflow-hidden relative bg-center bg-cover"
            >
                <div className="relative z-10">
                    <h1 className="text-3xl sm:text-4xl font-bold mb-2 text-white">
                        Pesan <span className="text-brand">Kopi</span><br />
                        Tanpa Antri.
                    </h1>
                    <p className="text-muted max-w-sm">
                        Pilih menu favoritmu, bayar via QRIS, dan ambil pesananmu saat sudah siap.
                    </p>
                </div>
            </div>

            {/* Category Filter */}
            <CategoryTabs
                categories={categories}
                activeCategory={activeCategory}
                onSelect={setActiveCategory}
            />

            {/* Error State */}
            {error && (
                <div className="mt-8 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-center">
                    Gagal memuat menu: {error}
                </div>
            )}

            {/* Product Grid */}
            <div className="mt-6 mb-8">
                {isLoading ? (
                    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                        {[1, 2, 3, 4, 5, 6, 7, 8].map(i => (
                            <div key={i} className="bg-card rounded-2xl h-72 border border-subtle shimmer"></div>
                        ))}
                    </div>
                ) : products.length > 0 ? (
                    <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                        {products.map(product => (
                            <ProductCard key={product.id} product={product} />
                        ))}
                    </div>
                ) : (
                    <div className="text-center py-12 text-muted">
                        Tidak ada menu yang tersedia di kategori ini.
                    </div>
                )}
            </div>
        </div>
    );
};

export default MenuPage;
