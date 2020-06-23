<?php

use Illuminate\Support\Facades\Route;

Route::get('/check/{sale}', 'CheckController@index');
Route::get('/relax', function () {
    return view('dontworry');
});
Route::get('/{any}', 'VueController@index')->where('any', '.*');
