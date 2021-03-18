<?php

namespace App\Providers;

use App\Http\Controllers\Services\TelegramService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;

class TelegramServiceProvider extends ServiceProvider
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
        App::bind('telegram_service', function () {
            return new TelegramService;
        });
    }
}
