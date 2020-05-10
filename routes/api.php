<?php

use Illuminate\Support\Facades\Route;

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
    Route::get('cart', 'api\CartController@getCart');
});

// TEST

Route::get('cart/test/{order}', 'api\CartController@sendTelegramMessage');

// END TEST

Route::get('order/{order}/accept', 'api\OrderController@accept');
Route::get('order/{order}/decline', 'api\OrderController@decline');

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
Route::resource('clients', 'api\ClientController');

// TransferController
Route::post('transfers/{transfer}/accept', 'api\TransferController@acceptTransfer');
Route::post('transfers/{transfer}/cancel', 'api\TransferController@declineTransfer');
Route::resource('transfers', 'api\TransferController');

//SaleController
Route::post('sales/{sale}/cancel', 'api\SaleController@cancelSale');
Route::post('sales', 'api\SaleController@store');

//ReportController
Route::get('reports', 'api\SaleController@reports');

Route::get('transfer/products', 'Services\TransferController@transferProducts');
Route::get('transfer/clients', 'Services\TransferController@transferClients');
