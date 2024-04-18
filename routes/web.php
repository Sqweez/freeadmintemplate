<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Route;

Route::prefix('console/artisan')->group(function () {
    Route::get('migrate', function () {
        Artisan::call('migrate');
        return response('ok');
    });
});

Route::get('/test', function () {
    // Начало измерения времени

    // Здесь ваш код

    // Конец измерения времени

    // Вычисление времени выполнения
    $withQuantity = function ($query) {
        return $query
            ->select(DB::raw('SUM(product_batches.quantity) as total_quantity'), 'product_sku.*')
            ->joinSub(function ($query) {
                $query->from('product_batches')
                    ->select('product_id', DB::raw('SUM(quantity) as quantity'))
                    //->where('quantity', '>', 0)
                    ->where('store_id', 1)
                    ->groupBy('product_id');
            },
                'product_batches', function ($join) {
                    $join->on('product_batches.product_id', '=', 'product_sku.id');
                });
    };

    /*$productSku = \App\v2\Models\ProductSku::query()
        ->with(['product' => function ($q) {
            return $q
                ->with(['category', 'manufacturer', 'subcategory', 'attributes']);
        }])
        ->with('attributes')
        ->limit(100)
        ->tap(function ($q) use ($function) {
            return $function($q);
        })
        ->get();*/

    $query = \App\v2\Models\ProductSku::query()
        ->with(['product' => function ($q) {
            return $q
                ->select(['id', 'product_name', 'category_id', 'manufacturer_id', 'subcategory_id'])
                ->with('category:id,category_name')
                ->with('subcategory:id,subcategory_name')
                ->with('manufacturer:id,manufacturer_name');
        }])
        ->with('margin_type:id,title')
        // Загрузка связанного продукта
        //->select('product_sku.id,product_id,product_barcode,margin_type_id,self_price', DB::raw('SUM(product_batches.quantity) as total_quantity'))
        ->tap($withQuantity)
        /* ->joinSub(function ($query) {
             $query->from('product_batches')
                 ->select('product_id', DB::raw('SUM(quantity) as quantity'))
                 ->where('quantity', '>', 0)
                 ->where('store_id', 1)
                 ->groupBy('product_id');
         }, 'product_batches', function ($join) {
             $join->on('product_batches.product_id', '=', 'product_sku.id');
         })*/
        ->groupBy('product_sku.id')
        /*        ->havingRaw('SUM(product_batches.quantity) > 0')*/
    ;

    return [
        'skus' => $query->get()
    ];
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
