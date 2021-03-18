<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class TelegramServiceFacade extends Facade {
    protected static function getFacadeAccessor() {
        return 'telegram_service';
    }
}
