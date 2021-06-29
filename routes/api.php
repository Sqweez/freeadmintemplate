<?php

use App\Http\Controllers\api\v2\ProductController;
use App\Http\Controllers\api\v2\CertificateController;
use App\Http\Controllers\api\v2\CompanionController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthorizationMiddleware;
use App\Http\Controllers\api\v2\FavoriteController;
use App\Http\Controllers\api\v2\KaspiController;
use App\Http\Controllers\api\ClientController;
use App\Http\Controllers\api\WaybillController;
use App\Http\Controllers\api\PromocodeController;
use App\Http\Controllers\api\v2\TaskController;
use App\Http\Controllers\api\v2\EducationController;
use App\Http\Controllers\api\AnalyticsController;

// Authorization

Route::post('auth', 'api\UserController@auth')->name('auth');
Route::post('login', 'api\UserController@login')->name('login');
Route::get('/unauthorised', function () {
    return response()->json(['error' => 'unauthorized']);
})->name('unauthorised');

Route::get('order/{order}/accept', 'api\v2\OrderController@accept');
Route::get('order/{order}/decline', 'api\v2\OrderController@decline');
Route::get('excel/products', 'api\ProductController@excelProducts');
Route::get('json/products/parse', 'api\ProductController@jsonParseProduct');
Route::get('set-tags', 'api\ProductController@setTags');

// HelpController
Route::get('set-partner-expired-at', 'HelpController@setPartnerExpiredAt');


Route::middleware(AuthorizationMiddleware::class)->group(function () {
    Route::prefix('shop')->group(function () {
        Route::post('analytics/search', 'api\AnalyticsController@storeSearch');
        Route::get('stores', 'api\StoreController@indexStores');
        Route::get('categories', 'api\CategoryController@indexShop');
        Route::get('products', 'api\shop\ProductController@getProducts');
        Route::get('products/search', 'api\shop\ProductController@getBySearch');
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
        Route::match(['get', 'post'], 'cart/total', 'api\CartController@getTotal');
        Route::get('cart', 'api\CartController@getCart');
        Route::get('cart/count', 'api\CartController@getCartCount');
        // ClientController
        Route::post('clients/login', 'api\ClientController@login');
        Route::post('clients/register', 'api\ClientController@register');
        Route::post('clients/auth', 'api\ClientController@getAuth');
        Route::post('clients/orders', 'api\ClientController@getOrders');
        Route::get('rating/sellers', 'api\RatingController@getRating');
        Route::post('rating/vote', 'api\RatingController@vote');
        Route::get('revision', 'api\RevisionController@index');
        Route::get('telegram/{order}', 'api\CartController@telegramMessage');
        Route::get('order/update/{order}', 'api\CartController@updateOrder');
        Route::get('order/amount/{order}', 'api\CartController@getOrderAmount');
        Route::get('partner', 'api\AnalyticsController@getPartnerSales');
        // BannerController
        Route::resource('banners', 'api\shop\BannerController');
        Route::post('favorite', [FavoriteController::class, 'toggleFavorite']);
        Route::get('favorite', [FavoriteController::class, 'index']);
        Route::get('hits', [\App\Http\Controllers\api\shop\ProductController::class, 'getHitProducts']);
        Route::get('promocode/search/{promocode}', [PromocodeController::class, 'searchPromocode']);
    });

    // RevisionController

    Route::get('revision/file/get', 'api\RevisionController@getRevisionProducts');
    Route::get('revision', 'api\RevisionController@getRevisions');
    Route::get('revision/{revision}', 'api\RevisionController@getRevisionInfo');
    Route::post('revision', 'api\RevisionController@createRevision');

    // End RevisionController

    Route::resource('sportsmen', 'api\SportsmenController');
    Route::resource('plans', 'api\PlanController');


    Route::get('setSlugs', 'api\CategoryController@slugs');

    Route::group(['namespace' => 'Services', 'prefix' => 'v1'], function () {
        Route::group(['prefix' => 'file'], function () {
            Route::post('upload', 'FileService@upload');
            Route::post('delete', 'FileService@delete');
        });
        Route::group(['prefix' => 'image'], function () {
            Route::match(['get', 'post'], 'thumb', 'ImageService@generateThumb');
        });

    });


    Route::resource('category', 'api\CategoryController');
    Route::resource('attributes', 'api\AttributeController');
    Route::resource('manufacturers', 'api\ManufacturerController');

    Route::prefix('stores')->group(function () {
        Route::get('types', 'api\StoreController@types');
        Route::resource('', 'api\StoreController');
        Route::post('balance/{store}', [CompanionController::class, 'addBalance']);
    });


    // ClientsController
    Route::resource('clients', 'api\ClientController');
    Route::post('clients/balance/{client}', 'api\ClientController@addBalance');

    // UserController
    Route::get('users/roles', 'api\UserController@indexRoles');
    Route::resource('users', 'api\UserController');

    // END UserController


    Route::resource('transfers', 'api\TransferController');

    //SaleController
    Route::post('sales/{sale}/cancel', 'api\SaleController@cancelSale');
    Route::post('sales', 'api\SaleController@store');
    Route::get('sales/brands/motivation', 'api\SaleController@getMotivationReport');

    //ReportController
    Route::patch('reports/{report}', 'api\SaleController@update');
    Route::get('reports', 'api\SaleController@reports');
    Route::get('reports/plan', 'api\SaleController@getPlanReports');
    Route::get('reports/total', 'api\SaleController@getTotal');
    Route::post('reports/products', 'api\SaleController@getReportProducts');


    Route::get('cart/group', 'api\CartController@groupCart');

    // TransferController
    Route::post('transfers/{transfer}/accept', 'api\TransferController@acceptTransfer');
    Route::post('transfers/{transfer}/cancel', 'api\TransferController@declineTransfer');

    Route::resource('goals', 'api\GoalController');

    Route::get('stats/mvp-products', 'api\StatsController@getMVPProducts');

    Route::post('excel/transfer/waybill', 'api\WaybillController@transferWaybill');
    Route::get('excel/transfer/waybill', 'api\WaybillController@transferWaybill');

    // RatingController

    Route::get('rating/sellers', 'api\RatingController@getSellers');
    Route::post('rating/sellers', 'api\RatingController@createSeller');
    Route::patch('rating/sellers/{seller}', 'api\RatingController@editSeller');
    Route::delete('rating/sellers/{seller}', 'api\RatingController@deleteSeller');

    Route::get('rating/criteria', 'api\RatingController@getCriteria');
    Route::post('rating/criteria', 'api\RatingController@createCriteria');
    Route::patch('rating/criteria/{crit}', 'api\RatingController@editCriteria');
    Route::delete('rating/criteria/{crit}', 'api\RatingController@deleteCriteria');

    // ArrivalController
    Route::post('arrivals/change/{arrival}', 'api\ArrivalController@changeArrival');
    Route::get('arrivals/cancel/{arrival}', 'api\ArrivalController@cancelArrival');
    Route::get('arrivals/{arrival}', 'api\ArrivalController@getArrival');
    Route::get('arrivals', 'api\ArrivalController@index');
    Route::post('arrivals', 'api\ArrivalController@createArrival');
    Route::post('arrivals/complete', 'api\ArrivalController@createBatch');
    Route::delete('arrivals/{arrival}', 'api\ArrivalController@deleteArrival');
    Route::patch('arrivals/{arrival}', 'api\ArrivalController@update');

    // AnalyticsController
    Route::get('analytics/partners', 'api\AnalyticsController@partners');
    Route::get('analytics/partners/{id}', 'api\AnalyticsController@partnerStats');

    // Promocode
    Route::get('promocode/search/{promocode}', 'api\PromocodeController@searchPromocode');
    Route::resource('promocode', 'api\PromocodeController');

    // Роуты v2

    Route::prefix('v2')->group(function () {

        Route::prefix('documents')->group(function () {
            Route::get('index', [WaybillController::class, 'getDocuments']);
            Route::post('waybill', [WaybillController::class, 'createWaybill']);
            Route::post('invoice', [WaybillController::class, 'createInvoice']);
            Route::post('invoice-payment', [WaybillController::class, 'createPaymentInvoice']);
            Route::get('batches/purchases', [WaybillController::class, 'getPurchasePrices']);
            Route::get('report/products', [WaybillController::class, 'getProductReport']);
            Route::post('products/check', [WaybillController::class, 'getProductCheck']);
            Route::post('price/list', [WaybillController::class, 'getPriceList']);
        });

        Route::prefix('products')->group(function () {
            Route::get('search', [ProductController::class, 'search']);
            Route::get('balance', [ProductController::class, 'getProductBalance']);
            Route::get('{id}/count', [ProductController::class, 'changeCount']);
            Route::get('/', [ProductController::class, 'index']);
            Route::get('/moderator', [ProductController::class, 'moderatorProducts']);
            Route::get('related', [ProductController::class, 'related']);
            Route::post('related', [ProductController::class, 'relatedCreate']);
            Route::get('{id}', [ProductController::class, 'show']);
            Route::post('/', [ProductController::class, 'store']);
            Route::patch('{product}', [ProductController::class, 'update']);
            Route::delete('{id}', [ProductController::class, 'delete']);
            Route::get('quantity/{store}', [ProductController::class, 'getProductsQuantity']);
            Route::post('{id}/quantity', [ProductController::class, 'addProductQuantity']);
            //Добавление ассортимента товара
            Route::post('{product}/sku', [ProductController::class, 'createProductSku']);
            Route::patch('{sku}/sku', [ProductController::class, 'updateProductSku']);
        });

        Route::prefix('certificates')->group(function () {
            Route::post('/', [CertificateController::class, 'create']);
            Route::get('/', [CertificateController::class, 'index']);
            Route::delete('/{id}', [CertificateController::class, 'delete']);
        });

        Route::prefix('images')->group(function () {
            Route::get('category', 'api\ImageController@category');
            Route::get('products', 'api\ImageController@products');
        });

        Route::prefix('kaspi')->group(function () {
            Route::get('products/xml', [KaspiController::class, 'getProductsXML']);
            Route::get('orders', [KaspiController::class, 'getOrders']);
            Route::get('analytics', [KaspiController::class, 'getAnalytics']);
        });

        Route::prefix('cron')->group(function () {
            Route::get('order/messages', 'api\v2\CronController@orderMessages');
            Route::get('order/cancel', 'api\v2\CronController@cancelOrders');
        });

        Route::prefix('orders')->group(function () {
            Route::get('/', 'api\v2\OrderController@getOrders');
            Route::delete('/{order}', 'api\v2\OrderController@deleteOrder');
            Route::patch('/{order}', 'api\v2\OrderController@update');
            Route::post('/{order}/image', 'api\v2\OrderController@setImage');
            Route::get('/{id}', 'api\v2\OrderController@getOrder');
            Route::patch('/client/{order}', 'api\v2\OrderController@changeClient');
            Route::post('/products/{order}', 'api\v2\OrderController@changeProducts');
        });

        Route::prefix('news')->group(function () {
            Route::get('/', 'api\v2\NewsController@index');
            Route::post('/', 'api\v2\NewsController@store');
            Route::patch('/{news}', 'api\v2\NewsController@update');
            Route::delete('/{id}', 'api\v2\NewsController@destroy');
        });

        Route::prefix('suppliers')->group(function () {
            Route::post('/', 'api\v2\SupplierController@store');
            Route::get('/', 'api\v2\SupplierController@index');
            Route::patch('/{id}', 'api\v2\SupplierController@update');
            Route::delete('/{id}', 'api\v2\SupplierController@destroy');
        });

        Route::prefix('clients')->group(function () {
            Route::get('analytics', [ClientController::class, 'getClientAnalytics']);
        });

        Route::get('cities', [\App\Http\Controllers\api\StoreController::class, 'getCities']);

        Route::prefix('tasks')->group(function () {
            Route::get('/', [TaskController::class, 'index']);
            Route::get('/current', [TaskController::class, 'getCurrentTasks']);
            Route::post('/', [TaskController::class, 'store']);
            Route::patch('/{id}', [TaskController::class, 'update']);
            Route::patch('/status/{id}', [TaskController::class, 'editTaskStatus']);
            Route::delete('/{id}', [TaskController::class, 'destroy']);
        });

        Route::prefix('educations')->group(function() {
            Route::get('/', [EducationController::class, 'index']);
            Route::post('/', [EducationController::class, 'store']);
            Route::patch('/{id}', [EducationController::class, 'update']);
            Route::delete('/{id}', [EducationController::class, 'destroy']);
        });

        Route::get('brands/analytics', [AnalyticsController::class, 'getBrandSales']);
    });
});
