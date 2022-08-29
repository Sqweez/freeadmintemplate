<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::prefix('console/artisan')->group(function () {
    Route::get('migrate', function () {
        Artisan::call('migrate');
        return response('ok');
    });
});

Route::prefix('print')->group(function () {
    Route::get('check/{sale}', [PrintController::class, 'printCheck']);
    Route::get('barcode/{id}', [PrintController::class, 'printBarcode']);
    Route::get('price/{id}', [PrintController::class, 'printPrice']);
});


Route::get('/{any}', 'VueController@index')->where('any', '.*');
