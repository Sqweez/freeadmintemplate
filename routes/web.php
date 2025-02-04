<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;


Route::get('/test', function () {
   Artisan::call(\App\Console\Commands\EcommercePriceList\Halyk\CreateHalykExcelPriceCommand::class);
});

Route::get('halyk/price', function () {
    $disk = Storage::disk('public');
    $filename = 'halyk/excel/halyk_products_1.xlsx';

    if (!$disk->exists($filename)) {
        abort(404);
    }

    return new StreamedResponse(function () use ($disk, $filename) {
        $stream = $disk->readStream($filename);
        fpassthru($stream);
        fclose($stream);
    }, 200, [
        'Content-Type'        => $disk->mimeType($filename),
        'Content-Disposition' => 'attachment; filename="' . basename($filename) . '"',
        'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
        'Pragma'              => 'no-cache',
        'Expires'             => '0',
    ]);
    $path = storage_path("app/public/halyk/excel/halyk_products_1.xlsx");

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path, [
        'Content-Type'        => mime_content_type($path),
        'Cache-Control'       => 'no-store, no-cache, must-revalidate, max-age=0',
        'Pragma'              => 'no-cache',
        'Expires'             => '0',
        'Last-Modified'       => gmdate('D, d M Y H:i:s', filemtime($path)) . ' GMT',
    ]);
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
