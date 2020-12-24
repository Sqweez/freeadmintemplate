<?php

namespace App\Providers;

use App\Http\Controllers\Services\ProductService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        App::bind('product_service', function () {
            return new ProductService;
        });
    }
}
