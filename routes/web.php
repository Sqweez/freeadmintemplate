<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::prefix('console/artisan')->group(function () {
    Route::get('migrate', function () {
        Artisan::call('migrate');
        return response('ok');
    });
});

Route::get('orders/{order}/whatsapp', function (\App\Order $order) {
    $phone = \Str::replaceFirst('+', '', $order['phone']);
    $message = 'Здравствуйте, Ваш заказ принят и передан курьеру. Ожидайте доставку c ?? до ??. (с) Служба заботы о клиентах “Iron addicts”';
    $link = 'https://api.whatsapp.com/send?phone='. $phone .'&text=' . urlencode($message);
    return Redirect::to($link);
});

Route::prefix('print')->group(function () {
    Route::get('check/{sale}', [PrintController::class, 'printCheck']);
    Route::get('barcode/{id}', [PrintController::class, 'printBarcode']);
    Route::get('price/{id}', [PrintController::class, 'printPrice']);
});


Route::get('/{any}', 'VueController@index')->where('any', '.*');
