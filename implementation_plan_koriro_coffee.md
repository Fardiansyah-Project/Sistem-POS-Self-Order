```markdown
# Dokumen Perencanaan Implementasi Sistem
## Sistem POS Self-Order Koriro Coffee Tondo PWA Berbasis WMA

### 1. Tahapan Implementasi Proyek
1. **Tahap Inisialisasi & Perancangan Sistem (Minggu 1 - 2)**
   - Analisis kebutuhan sistem dan perancangan ERD (Entity Relationship Diagram).
   - Desain skema database relasional untuk produk, kategori, transaksi, detail pesanan, bahan baku, resep, dan riwayat peramalan.
   - Perancangan arsitektur API backend Laravel dan struktur komponen React.

2. **Tahap Pengembangan Backend & API (Minggu 3 - 5)**
   - Konfigurasi proyek Laravel dan pengaturan koneksi database MySQL.
   - Pembuatan migrasi database, model, dan seeder.
   - Pengembangan endpoint API untuk autentikasi, manajemen menu, keranjang, dan transaksi pemesanan.
   - Implementasi logika perhitungan metode *Weighted Moving Average* (WMA) untuk peramalan bahan baku.
   - Integrasi pustaka pembayaran Midtrans Core API / Snap.

3. **Tahap Pengembangan Frontend User & PWA (Minggu 6 - 8)**
   - Inisialisasi aplikasi React dengan penataan styling menggunakan Tailwind CSS.
   - Pengembangan antarmuka katalog menu, keranjang belanja, dan halaman status pesanan.
   - Integrasi API backend Laravel ke React menggunakan Axios.
   - Konfigurasi PWA (Web App Manifest dan Service Worker) agar aplikasi dapat diinstal dan mendukung mode offline dasar.
   - Integrasi pembayaran Midtrans Snap pada sisi klien React.

4. **Tahap Pengembangan Panel Admin & Kasir (Minggu 9 - 10)**
   - Pengembangan layout panel admin menggunakan Bootstrap, jQuery, dan Blade templating Laravel.
   - Implementasi fitur manajemen produk, kategori, dan resep bahan baku.
   - Pembuatan halaman pemantauan pesanan (Kitchen/Cashier Dashboard) dan laporan penjualan.
   - Implementasi tampilan visual hasil peramalan bahan baku WMA.

5. **Tahap Pengujian & Evaluasi (Minggu 11 - 12)**
   - Pengujian fungsionalitas sistem (Black-box testing) pada modul self-order, pembayaran Midtrans, dan kalkulasi WMA.
   - Pengujian performa PWA dan kompatibilitas perangkat mobile.
   - Perbaikan bug dan penyusunan laporan skripsi.

### 2. Struktur Tabel Database Utama (MySQL)
| Nama Tabel | Deskripsi Kolom Utama |
| :--- | :--- |
| `users` | id, name, email, password, role (admin/kasir) |
| `categories` | id, name, slug |
| `products` | id, category_id, name, price, image, description, stock |
| `ingredients` | id, name, unit (gram/ml/pcs), stock |
| `recipes` | id, product_id, ingredient_id, quantity_needed |
| `transactions` | id, order_code, customer_name, total_amount, payment_status, midtrans_transaction_id, created_at |
| `transaction_details` | id, transaction_id, product_id, quantity, subtotal |
| `raw_material_forecasts` | id, ingredient_id, period_date, forecasted_amount, wma_weight_details |

### 3. Konfigurasi Endpoint API Utama (Laravel)
```text
- POST   /api/v1/auth/login          -> Autentikasi Admin/Kasir
- GET    /api/v1/products            -> Mengambil daftar menu produk
- GET    /api/v1/categories          -> Mengambil daftar kategori menu
- POST   /api/v1/orders              -> Membuat pesanan baru & inisialisasi Midtrans Snap
- POST   /api/v1/payments/webhook    -> Endpoint webhook notifikasi status pembayaran Midtrans
- GET    /api/v1/forecast/wma        -> Menjalankan dan mengambil hasil kalkulasi peramalan WMA bahan baku