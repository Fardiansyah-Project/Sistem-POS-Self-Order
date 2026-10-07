<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\OrderStatusController;
use App\Http\Controllers\Api\V1\ForecastController;
use App\Http\Controllers\Api\V1\OrderAvailabilityController;

Route::prefix('v1')->group(function () {
    
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::get('order-availability', [OrderAvailabilityController::class, 'show']);
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{slug}', [ProductController::class, 'show']);

    Route::post('orders', [OrderController::class, 'store']);
    Route::post('orders/{order_code}/payment-token', [OrderController::class, 'paymentToken']);

    Route::get('orders/{order_code}/status', [OrderStatusController::class, 'show']);

    Route::post('payments/webhook', [PaymentController::class, 'webhook']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);

        Route::get('forecast/wma', [ForecastController::class, 'index']);
    });
});
