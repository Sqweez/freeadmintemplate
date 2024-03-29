<?php

use App\Http\Controllers\api\opt\v1\AuthController;
use App\Http\Controllers\api\opt\v1\CatalogueController;
use App\Http\Middleware\OptAuthMiddleware;

Route::prefix('opt')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::post('auth/register', [AuthController::class, 'register']);
        Route::get('auth/me', [AuthController::class, 'me'])->middleware([OptAuthMiddleware::class]);
        Route::post('auth/login', [AuthController::class, 'login']);
        Route::prefix('/catalogue')->group(function () {
            Route::get('/', [CatalogueController::class, 'getCatalogEntities']);
            Route::get('/products', [CatalogueController::class, 'getProducts']);
        });
    });
});
