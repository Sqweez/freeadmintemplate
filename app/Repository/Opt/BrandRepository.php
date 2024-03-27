<?php

namespace App\Repository\Opt;

use App\Http\Resources\Opt\BrandResource;
use App\Manufacturer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BrandRepository extends BaseRepository
{
    public function get(): AnonymousResourceCollection
    {
        $brands =  $this->getFromCache(function () {
            return Manufacturer::query()
                ->tap(function ($query) {
                    return $this->havingOptProducts($query);
                })
                ->get();
        });

        return BrandResource::collection($brands);
    }
}
