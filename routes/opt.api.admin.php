<?php

use App\Http\Controllers\api\opt\admin\v1\ClientController;
use App\Http\Controllers\api\opt\admin\v1\OrderController;
use App\Http\Middleware\AuthorizationMiddleware;

Route::prefix('admin')->group(function () {
    Route::prefix('v1')->middleware([AuthorizationMiddleware::class])->group(function () {
        Route::prefix('client')->group(function () {
            Route::get('/', [ClientController::class, 'get']);
        });
        Route::prefix('order')->group(function () {
            Route::get('/', [OrderController::class, 'get']);
            Route::get('/{order}', [OrderController::class, 'show']);
            Route::patch('/{order}', [OrderController::class, 'update']);
            Route::patch('/products/{order}', [OrderController::class, 'updateOrderProducts']);
            Route::post('/{order}/waybill', [OrderController::class, 'uploadWaybill']);
            Route::post('/{order}/invoice', [OrderController::class, 'uploadInvoice']);
        });
    });
});
