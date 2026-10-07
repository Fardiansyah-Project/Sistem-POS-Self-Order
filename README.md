# Koriro Coffee — POS Self-Order & Forecasting

Aplikasi Laravel API/admin dashboard dan React PWA untuk POS self-order, Midtrans Snap, serta peramalan bahan baku WMA. Zona waktu aplikasi: **Asia/Makassar**.

## Persyaratan dan instalasi

Persyaratan: PHP 8.3+, Composer, Node.js/npm yang kompatibel dengan Vite 8, dan MySQL/MariaDB (atau database yang kompatibel dengan query project).

1. Install dependency: `composer install` dan `npm install`.
2. Salin `.env.example` menjadi `.env`; atur `APP_URL`, database (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), `APP_TIMEZONE=Asia/Makassar`, dan kredensial Midtrans yang dibaca `config/services.php`.
3. Buat database, lalu jalankan:

```bash
php artisan key:generate
php artisan migrate --seed
npm run build
```

4. Jalankan `php artisan serve`. Untuk development frontend jalankan `npm run dev` di terminal terpisah.

Pastikan `config/app.php` membaca `'timezone' => env('APP_TIMEZONE', 'Asia/Makassar')`; setelah mengubah konfigurasi jalankan `php artisan config:clear`. Gunakan Midtrans Sandbox saat development. URL webhook Midtrans: `https://<domain-anda>/api/v1/payments/webhook` (harus bisa diakses publik melalui HTTPS). Periksa `database/seeders/UserSeeder.php` untuk akun awal dan ganti password sebelum deployment. Admin: `/login`, dashboard: `/cms/admin`.

## Fitur dan PWA

Customer PWA tersedia di `/`; fitur admin mencakup produk, kategori, bahan baku, resep, POS, dapur, transaksi, laporan, dan forecast WMA. Manifest: `public/manifest.json`; service worker: `public/sw.js`. Instalasi PWA memerlukan HTTPS (localhost dapat digunakan untuk development). Setelah perubahan frontend jalankan `npm run build` dan reload untuk memperbarui aset service worker. Untuk pengujian offline, buka aplikasi online terlebih dahulu lalu gunakan mode offline DevTools. Checkout dan pembayaran tetap memerlukan koneksi jaringan—jangan mengandalkan cache untuk menyelesaikan transaksi.

## Pengujian dan production

Jalankan `php artisan test`. Production wajib menggunakan HTTPS, `APP_ENV=production`, `APP_DEBUG=false`, kredensial Midtrans production, backup database, dan hasil `npm run build`. Jangan commit `.env` maupun secret ke Git.

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
