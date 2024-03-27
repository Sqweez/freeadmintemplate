<?php

namespace App\Repository\Opt;

abstract class BaseRepository
{
    public $ttl = 60 * 10;
    public string $cacheKey;

    public function __construct()
    {
        $this->cacheKey = get_class($this);
    }

    abstract public function get();

    public function getFromCache($callback)
    {
        return \Cache::remember($this->cacheKey, $this->ttl, $callback);
    }

    public function havingOptProducts($queryBuilder)
    {
        return $queryBuilder->whereHas('products', function ($query) {
            $query->optProducts();
        });
    }
}
