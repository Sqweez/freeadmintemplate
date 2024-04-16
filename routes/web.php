<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::prefix('console/artisan')->group(function () {
    Route::get('migrate', function () {
        Artisan::call('migrate');
        return response('ok');
    });
});


Route::get('without-reviews', function () {
    $products =  \App\v2\Models\Product::query()
        ->whereHas('sku', function ($q) {
            return $q->whereHas('margin_type', function ($q) {
                return $q->where('title', 'LIKE', strtoupper(request()->get('type', 'A')));
            });
        })
        ->whereDoesntHave('comments')
        ->with('manufacturer')
        ->get()
        ->map(function (\App\v2\Models\Product $product) {
            return [
                'id' => $product->id,
                'name' => $product->product_name,
                'brand' => $product->manufacturer->manufacturer_name,
            ];
        });

    $jsonData = json_encode($products);
    $response = Response::make($jsonData);
    $response->header('Content-Type', 'application/json; charset=utf-8');
    $response->header('Content-Disposition', 'attachment; filename=товары-без-отзывов-' . strtoupper(request()->get('type', 'A')) . '.json');
    return $response;
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

Route::group(['prefix' => 'fit'], function () {
    Route::get('/{any}', 'VueController@fit')->where('any', '.*');
    Route::get('/', 'VueController@fit')->where('any', '.*');
});

Route::get('debugbar', function () {
    return view('debugbar');
});


Route::get('/{any}', 'VueController@index')->where('any', '.*');
