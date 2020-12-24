<?php

use Illuminate\Support\Facades\Route;

Route::get('/check/{sale}', 'CheckController@index');
Route::get('/relax', function () {
    return view('dontworry');
});

Route::get('/test', 'TestController@index');
Route::get('/test-2', 'TestController@index2');

Route::get('/{any}', 'VueController@index')->where('any', '.*');
