<?php



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
