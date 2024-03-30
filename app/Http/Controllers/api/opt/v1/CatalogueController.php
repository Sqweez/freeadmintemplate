<?php

namespace App\Http\Controllers\api\opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\ProductResource;
use App\Repository\Opt\BrandRepository;
use App\Repository\Opt\CategoryRepository;
use App\Resolvers\Meta\OptMetaCatalogResolver;
use App\Resolvers\Opt\OptCatalogFiltersResolver;
use App\Resolvers\Opt\OptCatalogProductResolver;
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

    public function getProducts(
        Request $request,
        OptCatalogFiltersResolver $filtersResolver,
        OptCatalogProductResolver $productResolver,
        OptMetaCatalogResolver $metaCatalogResolver
    ): JsonResponse
    {
        $filters = $filtersResolver->resolve($request->all());
        $client = auth()->user();
        $productQuery = $productResolver->getProductQuery($filters, $client);
        $products = $productQuery->tap($productResolver->attachAdditionalEntities($productQuery));
        return $this->respondSuccessNoReport([
            'products' => ProductResource::collection($products->get()),
            'meta' => $metaCatalogResolver->resolver($filters),
            'client' => $client,
            'filters' => $productResolver->getFilters($productQuery)
        ]);
    }
}
