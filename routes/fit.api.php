<?php

// роуты для фитнес-залов
use App\Http\Controllers\api\Fit\v1\AuthController;
use App\Http\Controllers\api\Fit\v1\ClientController;
use App\Http\Controllers\api\Fit\v1\ProductController;
use App\Http\Controllers\api\Fit\v1\ServiceController;
use App\Http\Controllers\api\Fit\v1\StatsController;
use App\Http\Middleware\FitnessAuthorizationMiddleware;


Route::prefix('fit')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::post('auth/login', [AuthController::class, 'login']);
        Route::middleware(FitnessAuthorizationMiddleware::class)->group(function () {
            Route::prefix('auth')->group(function () {
                Route::get('me', [AuthController::class, 'me']);
            });
            Route::apiResource('users', 'api\Fit\v1\UserController');
            Route::get('clients/search', [ClientController::class, 'search']);
            Route::post('clients/{client}/top-up', [ClientController::class, 'topUpBalance']);
            Route::apiResource('clients', 'api\Fit\v1\ClientController');
            Route::prefix('services')->group(function () {
                Route::apiResource('/', 'api\Fit\v1\ServiceController');
                Route::post('/{sale}/visit', [ServiceController::class, 'createVisit']);
                Route::post('sales', [ServiceController::class, 'createSale']);
            });

            Route::prefix('products')->group(function () {
                Route::get('/', [ProductController::class, 'index']);
                Route::post('/sale', [ProductController::class, 'sale']);
            });

            Route::prefix('stats')->group(function () {
                Route::get('/dashboard', [StatsController::class, 'getDashboardStats']);
            });
        });
    });
});
