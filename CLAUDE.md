# CLAUDE.md - Guideline & Development Context

## Project Overview
- **Name**: POS Self-Order Koriro Coffee Tondo PWA with Weighted Moving Average (WMA) Forecasting
- **Tech Stack**: 
  - Backend: Laravel 11 (RESTful API, MySQL, Eloquent, WMA Forecasting Logic, Midtrans Integration)
  - User Frontend: React.js, Tailwind CSS, PWA (Service Worker, Manifest)
  - Admin/Cashier Frontend: Bootstrap 5, jQuery, Laravel Blade Templating

## Architecture & Conventions
- **API-First Design**: Laravel handles database, business logic, authentication, WMA calculation, and Midtrans webhooks. React consumes these APIs via Axios.
- **Naming Conventions**: 
  - Database tables: snake_case (`transactions`, `raw_material_forecasts`)
  - Controllers/Models: PascalCase (`ProductController`, `Ingredient`)
  - React Components: PascalCase (`MenuCatalog.jsx`, `CartDrawer.jsx`)
- **Coding Style**:
  - Keep functions modular and well-documented for thesis evaluation.
  - Ensure strict separation between Admin panel (Blade/Bootstrap) and Customer Self-Order (React/Tailwind).

## Common Commands
- **Backend (Laravel)**:
  - `php artisan serve` - Run backend local server
  - `php artisan migrate --seed` - Run database migrations & seeders
  - `php artisan test` - Run PHPUnit tests
- **Frontend (React)**:
  - `npm run dev` - Run Vite development server
  - `npm run build` - Build production bundle for PWA