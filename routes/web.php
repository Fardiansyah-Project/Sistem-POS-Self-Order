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
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\RecipeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Api\V1\OrderAvailabilityController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ═══════════════════════════════════════════════════════════════════════════
// Route Admin Panel CMS
// ═══════════════════════════════════════════════════════════════════════════
Route::prefix('cms/admin')->middleware('auth:web,sanctum')->group(function () {

    // ─────────────────────────────────────────────────────────────────────
    // VIEW ROUTES — Render skeleton Blade saja (tanpa data server-side).
    // Semua data dimuat via jQuery/AJAX ke endpoint API di bawah.
    // ─────────────────────────────────────────────────────────────────────
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');

    // Kategori
    Route::get('/categories', fn() => view('admin.categories.index'))->name('admin.categories.index');

    // Produk
    Route::get('/products', fn() => view('admin.products.index'))->name('admin.products.index');
    Route::get('/products/create', fn() => view('admin.products.create'))->name('admin.products.create');
    Route::get('/products/{id}/edit', fn($id) => view('admin.products.edit', compact('id')))->name('admin.products.edit');

    // Bahan Baku
    Route::get('/ingredients', fn() => view('admin.ingredients.index'))->name('admin.ingredients.index');

    // Resep (nested di bawah produk)
    Route::get('/products/{productId}/recipes', fn($productId) => view('admin.recipes.index', compact('productId')))->name('admin.recipes.index');

    // Halaman single-page
    Route::get('/transactions', fn() => view('admin.transactions.index'))->name('admin.transactions.index');
    Route::get('/kitchen', fn() => view('admin.kitchen.index'))->name('admin.kitchen.index');
    Route::get('/pos', fn() => view('admin.pos.index'))->name('admin.pos.index');
    Route::get('/forecast', fn() => view('admin.forecast.index'))->name('admin.forecast.index');
    Route::get('/reports', fn() => view('admin.reports.index'))->name('admin.reports.index');

    Route::prefix('api')->name('admin.api.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'data'])->name('dashboard');
        Route::get('/order-availability', [OrderAvailabilityController::class, 'show'])->name('order-availability.show');
        Route::patch('/order-availability', [OrderAvailabilityController::class, 'update'])->name('order-availability.update');

        // Categories CRUD
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Products CRUD (POST untuk update karena file upload tidak support PUT native)
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
        Route::post('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Ingredients CRUD
        Route::get('/ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
        Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
        Route::get('/ingredients/{ingredient}', [IngredientController::class, 'show'])->name('ingredients.show');
        Route::put('/ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
        Route::delete('/ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');

        // Recipes (nested di bawah products)
        Route::get('/products/{product}/recipes', [RecipeController::class, 'index'])->name('recipes.index');
        Route::post('/products/{product}/recipes', [RecipeController::class, 'store'])->name('recipes.store');
        Route::delete('/products/{product}/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

        // Transactions
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::delete('/transactions/bulk-destroy', [TransactionController::class, 'bulkDestroy'])->name('transactions.bulkDestroy');
        Route::patch('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
        Route::patch('/transactions/{transaction}/close', [TransactionController::class, 'close'])->name('transactions.close');

        // Kitchen
        Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
        Route::patch('/kitchen/{transaction}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.update');

        // POS
        Route::get('/pos/products', [PosController::class, 'products'])->name('pos.products');
        Route::post('/pos/store', [PosController::class, 'store'])->name('pos.store');

        // Forecast WMA
        Route::get('/forecast', [ForecastController::class, 'data'])->name('forecast.data');
        Route::post('/forecast/run', [ForecastController::class, 'run'])->name('forecast.run');

        // Reports (tetap return file download, bukan JSON)
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/forecast', [ReportController::class, 'forecast'])->name('reports.forecast');
    });
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|admin).*$');
