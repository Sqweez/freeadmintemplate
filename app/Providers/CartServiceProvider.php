<?php

namespace App\Providers;

use App;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
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
        App::bind('cart_service', function () {
            return new App\Http\Controllers\Services\CartService();
        });
    }
}
