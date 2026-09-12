import { useState, useEffect } from 'react';
import { getCategories, getProducts } from '../services/api';

/**
 * useProducts — Fetch produk dan kategori dari API Laravel.
 * Handles loading state, error, dan filter by category.
 */
const useProducts = () => {
    const [categories, setCategories]         = useState([]);
    const [products, setProducts]             = useState([]);
    const [activeCategory, setActiveCategory] = useState(null); // null = semua
    const [isLoading, setIsLoading]           = useState(true);
    const [error, setError]                   = useState(null);

    // Load categories sekali saat mount
    useEffect(() => {
        getCategories()
            .then((res) => setCategories(res.data.data))
            .catch((err) => setError(err.message));
    }, []);

    // Load produk ketika activeCategory berubah
    useEffect(() => {
        setIsLoading(true);
        setError(null);

        getProducts(activeCategory)
            .then((res) => setProducts(res.data.data))
            .catch((err) => setError(err.message))
            .finally(() => setIsLoading(false));
    }, [activeCategory]);

    return {
        categories,
        products,
        activeCategory,
        setActiveCategory,
        isLoading,
        error,
    };
};

export default useProducts;
