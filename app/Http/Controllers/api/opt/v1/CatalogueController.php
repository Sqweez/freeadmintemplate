<?php

namespace App\Http\Controllers\api\opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\ProductResource;
use App\Http\Resources\Opt\SingleProductResource;
use App\Http\Resources\Opt\VariantResource;
use App\Repository\Opt\BrandRepository;
use App\Repository\Opt\CategoryRepository;
use App\Repository\Opt\VariantRepository;
use App\Resolvers\Meta\OptMetaCatalogResolver;
use App\Resolvers\Opt\OptCatalogFiltersResolver;
use App\Resolvers\Opt\OptCatalogProductResolver;
use App\Store;
use App\v2\Models\Currency;
use App\v2\Models\Product;
use App\v2\Models\WholesaleClient;
use App\v2\Models\WholesaleFavorite;
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
        $favorites = [];
        if (auth()->user()) {
            $favorites = WholesaleFavorite::whereWholesaleClientId(auth()->user()->id)->pluck('product_id')->toArray();
        }

        return $this->respondSuccess([
            'catalogue' => [
                'brands' => app(BrandRepository::class)->get(),
                'categories' => app(CategoryRepository::class)->get(),
                'currencies' => Currency::all(),
                'favorites' => $favorites
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
        if (!($client instanceof WholesaleClient)) {
            $client = null;
        }
        $productQuery = $productResolver->getProductQuery($filters, $client);
       /* $products = $productQuery->tap(function ($query) use ($productResolver) {
            return $productResolver->attachAdditionalEntities($query);
        });*/
        return $this->respondSuccessNoReport([
            'products' => ProductResource::collection($productQuery->get()),
            'meta' => $metaCatalogResolver->resolver($filters),
            'client' => $client,
            'filters' => $request->has('no-filters') ? null : $productResolver->getFilters($productQuery),
            'no_filters' => $request->has('no-filters')
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getProduct(Product $product, OptCatalogProductResolver $productResolver, Request $request): JsonResponse
    {
        $product->load('manufacturer');
        $product->load('category');
        $product->load('attributes');
        $product->load('product_images');
        $currency = $productResolver->retrieveCurrency(auth()->user());
        $product->load(['wholesale_prices' => function ($query) use ($currency) {
            return $query->where('currency_id', $currency);
        }]);
        $variants =
            app(VariantRepository::class)->get($product, auth()->user());
        $sameProducts = Product::query()
            ->where('is_opt', true)
            ->tap(function ($query) use ($productResolver) {
                return $productResolver->attachAdditionalEntities($query);
            })
            ->with([
                'wholesale_prices' => function ($query) use ($currency) {
                    return $query->where('currency_id', $currency);
                }
            ])
            ->with(['batches' => function ($q) {
                $wholesaleStoreId = Store::wholesaleStore()->pluck('id')->toArray();
                return $q
                    ->where('store_id', $wholesaleStoreId)
                    ->where('quantity', '>', 0);
            }])
            ->whereHas('batches', function ($q) {
                $wholesaleStoreId = Store::wholesaleStore()->pluck('id')->toArray();
                return $q
                    ->where('store_id', $wholesaleStoreId)
                    ->where('quantity', '>', 0);
            })
            ->whereKeyNot($product->id)
            ->where('category_id', $product->category_id)
            ->get();
        return $this->respondSuccessNoReport([
            'product' => SingleProductResource::make($product),
            'variants' => VariantResource::collection(
                $variants
            ),
            'in_stock' => $variants->count() > 0,
            'same_products' => ProductResource::collection($sameProducts),
            'user' => auth()->user(),
        ]);
    }

    public function getFavorites(OptCatalogProductResolver $productResolver): JsonResponse
    {
        $productQuery = $productResolver->getFavorites(auth()->user());
        if (!$productQuery) {
            return $this->respondError([
                'message' => 'Вы не авторизованы'
            ]);
        }
        return $this->respondSuccessNoReport([
            'favorites' => ProductResource::collection($productQuery->get()),
        ]);
    }

    public function toggleFavorite(Product $product): JsonResponse
    {

        $payload = [
            'wholesale_client_id' => auth()->user()->id,
            'product_id' => $product->id
        ];
       $favorite = WholesaleFavorite::where($payload)->first();
       if (!$favorite) {
           WholesaleFavorite::create([
               'wholesale_client_id' => auth()->user()->id,
               'product_id' => $product->id
           ]);
       } else {
           $favorite->delete();
       }
       return $this->respondSuccessNoReport([]);
    }
}
