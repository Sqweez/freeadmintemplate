<?php

namespace App\Http\Controllers\api\opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Repository\Opt\BrandRepository;
use App\Repository\Opt\CategoryRepository;
use App\Resolvers\Catalog\CatalogFiltersResolver;
use App\v2\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class CatalogueController extends BaseApiController
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getCatalogEntities(): JsonResponse
    {
        return $this->respondSuccess([
            'catalogue' => [
                'brands' => app(BrandRepository::class)->get(),
                'categories' => app(CategoryRepository::class)->get(),
                'currencies' => Currency::all(),
            ]
        ]);
    }

    public function getProducts(Request $request, CatalogFiltersResolver $filtersResolver)
    {
        $filters = $filtersResolver->resolve($request->all());
        return $filters;
    }
}
