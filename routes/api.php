<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\OrderStatusController;
use App\Http\Controllers\Api\V1\ForecastController;

/*
|--------------------------------------------------------------------------
| API Routes — Koriro Coffee Self-Order API v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // ─── Public Routes (tidak perlu auth) ───────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });

    // Menu publik (diakses oleh React customer PWA tanpa token)
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{slug}', [ProductController::class, 'show']);

    // Self-order (customer membuat pesanan tanpa perlu login)
    Route::post('orders', [OrderController::class, 'store']);

    // Status pesanan (untuk polling oleh React)
    Route::get('orders/{order_code}/status', [OrderStatusController::class, 'show']);

    // Webhook Midtrans (server-to-server, tidak butuh auth)
    Route::post('payments/webhook', [PaymentController::class, 'webhook']);

    // ─── Protected Routes (butuh Sanctum token) ──────────────────────────
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);

        // Forecast WMA — admin only
        Route::get('forecast/wma', [ForecastController::class, 'index']);
    });
});
