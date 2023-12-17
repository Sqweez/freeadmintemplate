<?php

// роуты для фитнес-залов
use App\Http\Controllers\api\Fit\v1\AuthController;
use App\Http\Controllers\api\Fit\v1\ClientController;
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
            Route::apiResource('clients', 'api\Fit\v1\ClientController');
        });

    });
});
