<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthorizationMiddleware;

// Authorization

Route::post('auth', 'api\UserController@auth')->name('auth');
Route::post('login', 'api\UserController@login')->name('login');
Route::get('/unauthorised', function () {
    return response()->json(['error' => 'unauthorized']);
})->name('unauthorised');

Route::get('order/{order}/accept', 'api\CartController@accept');
Route::get('order/{order}/decline', 'api\CartController@decline');

Route::middleware(AuthorizationMiddleware::class)->group(function () {
    Route::prefix('shop')->group(function () {
        Route::get('stores', 'api\StoreController@indexStores');
        Route::get('categories', 'api\CategoryController@indexShop');
        Route::get('products', 'api\shop\ProductController@getProducts');
        Route::get('products-group', 'api\shop\ProductController@groupProducts');
        Route::get('products/{product}', 'api\shop\ProductController@getProduct');
        Route::get('heading', 'api\shop\ProductController@getHeading');
        Route::get('slug', 'api\SlugController@index');
        Route::resource('manufacturers', 'api\ManufacturerController');
        Route::get('filters', 'api\shop\ProductController@filters');
        // Cart Controller
        Route::post('cart/order', 'api\CartController@order');
        Route::post('cart/add', 'api\CartController@addCart');
        Route::post('cart/increase', 'api\CartController@increaseCount');
        Route::post('cart/decrease', 'api\CartController@decreaseCount');
        Route::post('cart/delete', 'api\CartController@deleteCart');
        Route::post('cart/total', 'api\CartController@getTotal');
        Route::get('cart', 'api\CartController@getCart');
        // ClientController
        Route::post('clients/login', 'api\ClientController@login');
        Route::post('clients/register', 'api\ClientController@register');
        Route::post('clients/auth', 'api\ClientController@getAuth');
    });

    // TEST

    Route::get('cart/test/{order}', 'api\CartController@sendTelegramMessage');

    // END TEST



    Route::get('mess', 'api\CartController@message');

    Route::get('setSlugs', 'api\CategoryController@slugs');

    Route::post('upload', 'Services\FileService@upload');
    Route::post('delete', 'Services\FileService@delete');

    // helpers

    Route::get('setThumbsAll', 'api\ProductController@setThumbsAll');

    //

    Route::resource('category', 'api\CategoryController');
    Route::post('products/batch', 'api\ProductController@createBatch');
    Route::post('products/range', 'api\ProductController@createRange');
    Route::resource('products', 'api\ProductController');
    Route::resource('attributes', 'api\AttributeController');
    Route::resource('manufacturers', 'api\ManufacturerController');
    Route::get('stores/types', 'api\StoreController@types');
    Route::resource('stores', 'api\StoreController');

    // ClientsController
    Route::resource('clients', 'api\ClientController');

    // UserController
    Route::get('users/roles', 'api\UserController@indexRoles');
    Route::resource('users', 'api\UserController');

    // END UserController


    Route::resource('transfers', 'api\TransferController');

    //SaleController
    Route::post('sales/{sale}/cancel', 'api\SaleController@cancelSale');
    Route::post('sales', 'api\SaleController@store');

    //ReportController
    Route::get('reports', 'api\SaleController@reports');
    Route::get('reports/total', 'api\SaleController@getTotal');


    Route::get('cart/group', 'api\CartController@groupCart');

    // TransferController
    Route::post('transfers/{transfer}/accept', 'api\TransferController@acceptTransfer');
    Route::post('transfers/{transfer}/cancel', 'api\TransferController@declineTransfer');
});





// TransferController
/*Route::get('transfer/products', 'Services\TransferController@transferProducts');
Route::get('transfer/clients', 'Services\TransferController@transferClients');*/
