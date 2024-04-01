<?php

use App\Http\Controllers\api\opt\v1\AuthController;
use App\Http\Controllers\api\opt\v1\CartController;
use App\Http\Controllers\api\opt\v1\CatalogueController;
use App\Http\Controllers\api\opt\v1\OrderController;
use App\Http\Middleware\OptAuthMiddleware;

Route::prefix('opt')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::post('auth/register', [AuthController::class, 'register']);
        Route::get('auth/me', [AuthController::class, 'me'])->middleware([OptAuthMiddleware::class]);
        Route::post('auth/login', [AuthController::class, 'login']);
        Route::patch('auth/profile', [AuthController::class, 'update'])->middleware([OptAuthMiddleware::class]);;
        Route::group(['middleware' => OptAuthMiddleware::class], function () {
            Route::prefix('/catalogue')->group(function () {
                Route::get('favorites', [CatalogueController::class, 'getFavorites']);
                Route::get('/', [CatalogueController::class, 'getCatalogEntities']);
                Route::get('/products', [CatalogueController::class, 'getProducts']);
                Route::get('/search', [CatalogueController::class, 'search']);
                Route::get('/product/{product}', [CatalogueController::class, 'getProduct']);
            });

            Route::prefix('cart')->group(function () {
                Route::post('/', [CartController::class, 'create']);
                Route::get('/total', [CartController::class, 'getTotal']);
                Route::get('/', [CartController::class, 'get']);
                Route::patch('/', [CartController::class, 'update']);
                Route::delete('/{product}', [CartController::class, 'delete']);
            });

            Route::prefix('order')->group(function () {
                Route::get('/history', [OrderController::class, 'getOrdersHistory']);
                Route::get('/history/{order}', [OrderController::class, 'getOrderProductsHistory']);
                Route::post('/', [OrderController::class, 'create']);
            });
        });
    });
});
