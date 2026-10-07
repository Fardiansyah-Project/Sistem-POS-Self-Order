import { create } from 'zustand';
import { persist } from 'zustand/middleware';

/**
 * useCart — Global cart state menggunakan Zustand dengan persistence ke localStorage.
 */
const useCart = create(
    persist(
        (set, get) => ({
            items: [],    // [{id, name, price, image_url, quantity, notes}]
            isOpen: false,
            toast: null,
            orderingOpen: true,

            setOrderingOpen: (orderingOpen) => set({ orderingOpen }),

            /** Tambah produk ke cart hanya saat pemesanan dibuka. */
            addItem: (product) => {
                if (!get().orderingOpen) return;
                set((state) => {
                    const existing = state.items.find((i) => i.id === product.id);
                    const toast = {
                        id: Date.now(),
                        productName: product.name,
                    };
                    if (existing) {
                        return {
                            items: state.items.map((i) =>
                                i.id === product.id
                                    ? { ...i, quantity: i.quantity + 1 }
                                    : i
                            ),
                            toast,
                        };
                    }
                    return {
                        items: [...state.items, { ...product, quantity: 1, notes: '' }],
                        toast,
                    };
                });
            },

            clearToast: () => set({ toast: null }),

            /** Kurangi quantity. Jika menjadi 0, hapus dari cart. */
            decrementItem: (productId) => {
                set((state) => ({
                    items: state.items
                        .map((i) =>
                            i.id === productId ? { ...i, quantity: i.quantity - 1 } : i
                        )
                        .filter((i) => i.quantity > 0),
                }));
            },

            /** Hapus produk dari cart */
            removeItem: (productId) => {
                set((state) => ({
                    items: state.items.filter((i) => i.id !== productId),
                }));
            },

            /** Update catatan item */
            updateNotes: (productId, notes) => {
                set((state) => ({
                    items: state.items.map((i) =>
                        i.id === productId ? { ...i, notes } : i
                    ),
                }));
            },

            /** Kosongkan cart */
            clearCart: () => set({ items: [] }),

            /** Buka/tutup cart drawer */
            openCart:  () => set({ isOpen: true }),
            closeCart: () => set({ isOpen: false }),
            toggleCart: () => set((state) => ({ isOpen: !state.isOpen })),

            // ─── Computed getters ───────────────────────────────────
            /** Total jumlah item di cart */
            getTotalItems: () => get().items.reduce((sum, i) => sum + i.quantity, 0),

            /** Total harga cart */
            getTotalPrice: () => get().items.reduce((sum, i) => sum + i.price * i.quantity, 0),
        }),
        {
            name: 'koriro-cart',
            partialize: (state) => ({ items: state.items }), // Status buka/tutup selalu diambil ulang dari server
        }
    )
);

export default useCart;
