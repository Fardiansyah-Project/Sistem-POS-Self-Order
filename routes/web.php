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

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Route Admin Panel CMS 
Route::prefix('cms/admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('products', ProductController::class, ['as' => 'admin']);
    Route::resource('categories', CategoryController::class, ['as' => 'admin']);
    Route::resource('ingredients', IngredientController::class, ['as' => 'admin']);

    // Resep Menu (Penting untuk WMA)
    Route::prefix('products/{product}/recipes')->name('admin.recipes.')->group(function () {
        Route::get('/', [RecipeController::class, 'index'])->name('index');
        Route::post('/', [RecipeController::class, 'store'])->name('store');
        Route::delete('/{recipe}', [RecipeController::class, 'destroy'])->name('destroy');
    });

    // Transaksi
    Route::prefix('/transactions')->name('admin.transactions.')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::delete('/bulk-destroy', [TransactionController::class, 'bulkDestroy'])->name('bulkDestroy');
        Route::patch('/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('cancel');
    });

    // Monitor Dapur
    Route::prefix('kitchen')->name('admin.kitchen.')->group(function () {
        Route::get('/', [KitchenController::class, 'index'])->name('index');
        Route::patch('/{transaction}/status', [KitchenController::class, 'updateStatus'])->name('update');
    });

    // Kasir POS
    Route::prefix('pos')->name('admin.pos.')->group(function () {
        Route::get('/', [PosController::class, 'index'])->name('index');
        Route::post('/', [PosController::class, 'store'])->name('store');
    });

    // Analisis WMA Forecast
    Route::prefix('forecast')->name('admin.forecast.')->group(function () {
        Route::get('/', [ForecastController::class, 'index'])->name('index');
        Route::post('/run', [ForecastController::class, 'run'])->name('run');
    });

    // Laporan 
    Route::prefix('reports')->name('admin.reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/forecast', [ReportController::class, 'forecast'])->name('forecast');
    });
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!api|admin).*$');
