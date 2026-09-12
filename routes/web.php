<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ForecastController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\KitchenController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\IngredientController;

// ─── Route Auth Web (Admin/Kasir Login) ───────────────────────────────
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Route Admin Panel (Web) ──────────────────────────────────────────
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Master Data Katalog
    Route::resource('products', ProductController::class, ['as' => 'admin']);
    Route::resource('categories', CategoryController::class, ['as' => 'admin']);
    
    // Resep Menu (Penting untuk WMA)
    Route::get('/products/{product}/recipes', [App\Http\Controllers\Admin\RecipeController::class, 'index'])->name('admin.recipes.index');
    Route::post('/products/{product}/recipes', [App\Http\Controllers\Admin\RecipeController::class, 'store'])->name('admin.recipes.store');
    Route::delete('/products/{product}/recipes/{recipe}', [App\Http\Controllers\Admin\RecipeController::class, 'destroy'])->name('admin.recipes.destroy');
    
    // Master Data Inventory
    Route::resource('ingredients', IngredientController::class, ['as' => 'admin']);

    // Transaksi
    Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
    
    // Monitor Dapur
    Route::get('/kitchen', [KitchenController::class, 'index'])->name('admin.kitchen.index');
    Route::patch('/kitchen/{transaction}/status', [KitchenController::class, 'updateStatus'])->name('admin.kitchen.update');

    // WMA Forecast
    Route::get('/forecast', [ForecastController::class, 'index'])->name('admin.forecast.index');
    Route::post('/forecast/run', [ForecastController::class, 'run'])->name('admin.forecast.run');
});

// Route untuk Customer PWA (React)
// Semua route yang tidak berawalan /api atau /admin akan ditangani oleh React Router
Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|admin).*$');
