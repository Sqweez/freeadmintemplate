<?php

use Illuminate\Support\Facades\Route;

Route::prefix('console/artisan')->group(function () {
    Route::get('migrate', function () {
        Artisan::call('migrate');
        return response('ok');
    });
});

Route::get('/check/{sale}', 'CheckController@index');

Route::get('/{any}', 'VueController@index')->where('any', '.*');
