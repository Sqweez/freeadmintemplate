<?php

namespace App\Http\Controllers\api\opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Repository\Opt\BrandRepository;
use App\Repository\Opt\CategoryRepository;

class CatalogueController extends BaseApiController
{

    public function index()
    {
        return $this->respondSuccess([
            'catalogue' => [
                'brands' => app(BrandRepository::class)->get(),
                'categories' => app(CategoryRepository::class)->get(),
            ]
        ]);
    }
}
