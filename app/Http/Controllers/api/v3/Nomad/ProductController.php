<?php

namespace App\Http\Controllers\api\v3\Nomad;

use App\Actions\Nomad\RetrieveAvailableNomadFiltersAction;
use App\Http\Controllers\Controller;
use App\Resolvers\Catalog\CatalogFiltersResolver;
use App\Resolvers\Catalog\CatalogProductQueryResolver;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        return [];
    }


    public function getNomadFilters(Request $request, RetrieveAvailableNomadFiltersAction $action)
    {
        $catalogFiltersResolver = app(CatalogFiltersResolver::class);
        $query = $request->all();
        $query['store_id'] = __hardcoded(1);
        $query['brands'] = '608';
        $filters = $catalogFiltersResolver->resolve($query);
        $productQueryResolver = app(CatalogProductQueryResolver::class);
        $ids = $productQueryResolver->resolve($filters, __hardcoded(1), $request->user_token)->select(['id'])->pluck('id');
        return $action->handle($ids);
    }
}
