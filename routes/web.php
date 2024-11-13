<?php

use App\Http\Controllers\PrintController;
use App\Service\Kaspi\KaspiOrdersApiService;
use Illuminate\Support\Facades\Route;

Route::get('kaspi-orders', function (KaspiOrdersApiService $apiService) {
    $response = $apiService->getOrders(pageNumber: 2, searchKey: 'eyJwYWdlIjp7Im51bWJlciI6MSwic2l6ZSI6MTB9LCJmaWx0ZXIiOnsib3JkZXJzIjp7ImNyZWF0aW9uRGF0ZSI6eyIkZ2UiOjE3MzEzNTE2MDAwMDAsIiRsZSI6MTczMTQyMTEzODAwMH0sInN0YXRlIjoiTkVXIn19fQ==');
    return $response;
    collect($response['data'])->each(function ($item) {
        \App\Models\Kaspi\KaspiOrder::create([
            'kaspi_id' => $item['id'],
            'attributes' => $item['attributes']
        ]);
    });
    $order = \App\Models\Kaspi\KaspiOrder::first();
    return $order->getOrderEntities();

});

Route::get('kaspi-test', function (KaspiOrdersApiService $apiService) {
    $orderCodes = [
        442101402,
        442107544,
        442112477,
        442124040,
        442133166,
        442159468,
        442166630,
        442164942,
        442170875,
        442168548,
        442179487,
        442186705,
        442183671,
        442190801,
        442201079,
        442200657,
        442208368,
        442210241,
        442211427,
        442216642,
        442222632,
        442221445,
        442221851,
        442230880,
        442249994,
        442267733,
        442278146,
        442292254,
        442295079,
        442297449,
        442308867,
        442310888,
        442319199,
        442327653,
        442326616,
        442330977,
        442349974,
        442352349,
        442355439,
        442363857,
        442365971,
        442379080,
        442381436,
        442382948,
        442388100,
        442390681,
        442395320,
        442394699,
        442400521,
        442414003,
        442411416,
        442420038,
        442428354,
        442442635,
        442446201,
        442448648,
        442454776,
        442461070,
        442490214,
        442489844,
        442504845,
        442508279,
        442505811,
        442504960,
        442516100,
        442517234,
        442522325,
        442536143,
        442536569,
        442540610,
        442547844,
        442551062,
        442558420,
        442562651,
        442590184,
        442592055,
        442597715,
        442602280,
        442604059,
        442607601,
        442604844,
        442608761,
        442615293,
        442618642,
        442625416,
        442628035,
        442632706,
        442633640,
        442640585,
        442658006,
        442662036,
        442669855,
        442675263,
        442675759,
        442674997,
        442684637,
        442693467,
        442701017,
        442703136,
        442706127,
        442712113,
        442713564,
        442711377,
        442714702,
        442723378,
        442731028,
        442734600,
        442747656,
        442753113,
        442752862,
        442755867,
        442761700,
        442761909,
        442767609,
        442774254,
        442794226,
        442804368,
        442808868,
        442823435,
        442826532,
        442825614,
        442830472,
        442830564,
        442835299,
        442848435,
        442853417,
        442857791,
        442885195,
        442893160,
        442890922,
        442905494,
        442904525,
        442916913,
        442917879,
        442925870,
        442944531,
        442945941,
        442947973,
        442957115,
        442959247,
        442962737,
        442963108,
        442964183,
        442965333,
        442969242,
        442974257,
        442979233,
        442981672,
        442983518,
        442985237,
        442987722,
        442990617,
        442997745,
        443002471,
        443003886,
        443005487,
        443015247,
        443026980,
        443027104,
        443029863,
        443030035,
        443031118
    ];
    $response = $apiService->getOrderById($orderCodes[0]);
return $response;
    foreach ($orderCodes as $orderCode) {
        $response = $apiService->getOrderById($orderCode);
        $orderId = data_get($response, 'data.0.id', null);
        $entries = $apiService->getOrderEntries($orderId);
        if (count($entries['data']) > 1) {
            dd($orderCode, $entries);
        }
    }

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
