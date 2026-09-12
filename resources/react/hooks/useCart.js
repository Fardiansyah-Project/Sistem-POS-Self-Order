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

            /** Tambah produk ke cart. Jika sudah ada, increment quantity. */
            addItem: (product) => {
                set((state) => {
                    const existing = state.items.find((i) => i.id === product.id);
                    if (existing) {
                        return {
                            items: state.items.map((i) =>
                                i.id === product.id
                                    ? { ...i, quantity: i.quantity + 1 }
                                    : i
                            ),
                        };
                    }
                    return {
                        items: [...state.items, { ...product, quantity: 1, notes: '' }],
                    };
                });
            },

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
            partialize: (state) => ({ items: state.items }), // hanya simpan items
        }
    )
);

export default useCart;
