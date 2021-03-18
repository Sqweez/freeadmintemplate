<?php

use Illuminate\Support\Facades\Route;

Route::prefix('console/artisan')->group(function () {
    Route::get('migrate', function () {
        Artisan::call('migrate');
        return response('ok');
    });
});

Route::get('/check/{sale}', 'CheckController@index');
Route::get('/relax', function () {
    return view('dontworry');
});

Route::get('/bestsellers', 'TestController@index');
Route::get('/test-2', 'TestController@index2');

Route::get('/ungroupped', 'TestController@ungroupped');
Route::get('/ungroup/{id}', 'TestController@ungroupProduct');

Route::get('/{any}', 'VueController@index')->where('any', '.*');
