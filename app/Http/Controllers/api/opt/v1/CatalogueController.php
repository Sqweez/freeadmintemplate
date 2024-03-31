<?php

namespace App\Http\Controllers\api\opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\ProductResource;
use App\Http\Resources\Opt\SingleProductResource;
use App\Http\Resources\Opt\VariantResource;
use App\Repository\Opt\BrandRepository;
use App\Repository\Opt\CategoryRepository;
use App\Resolvers\Meta\OptMetaCatalogResolver;
use App\Resolvers\Opt\OptCatalogFiltersResolver;
use App\Resolvers\Opt\OptCatalogProductResolver;
use App\v2\Models\Currency;
use App\v2\Models\Product;
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
    ): JsonResponse {
        $filters = $filtersResolver->resolve($request->all());
        $client = auth()->user();
        $productQuery = $productResolver->getProductQuery($filters, $client);
        $products = $productQuery->tap(function ($query) use ($productResolver) {
            return $productResolver->attachAdditionalEntities($query);
        });
        return $this->respondSuccessNoReport([
            'products' => ProductResource::collection($products->get()),
            'meta' => $metaCatalogResolver->resolver($filters),
            'client' => $client,
            'filters' => $request->has('no-filters') ? null : $productResolver->getFilters($productQuery),
        ]);
    }

    public function search( Request $request,
        OptCatalogFiltersResolver $filtersResolver,
        OptCatalogProductResolver $productResolver): JsonResponse
    {
        $filters = $filtersResolver->resolve(['search' => $request->get('query')]);
        $client = auth()->user();
        $productQuery = $productResolver->getProductQuery($filters, $client);
        $products = $productQuery->tap(function ($query) use ($productResolver) {
            return $productResolver->attachAdditionalEntities($query);
        });
        return $this->respondSuccessNoReport([
            'products' => ProductResource::collection($products->get()),
        ]);
    }

    public function getProduct(Product $product): JsonResponse
    {
        $product->load('manufacturer');
        $product->load('category');
        $product->load('attributes');
        $product->load('product_images');
        return $this->respondSuccess([
            'product' => SingleProductResource::make($product),
            'variants' => VariantResource::collection(

            ),
        ]);
    }
}
