<?php


namespace App\Http\Controllers\Services;


class CacheService {

    public static function invalidateCache($key) {
        \Cache::forget($key);
    }


}
