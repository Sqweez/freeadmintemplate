<?php

namespace App\Http\Controllers\api\v3\Nomad;

use App\Http\Controllers\Controller;
use App\Resolvers\Catalog\CatalogFiltersResolver;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        return [];
    }


    public function getNomadFilters(Request $request)
    {
        $catalogFiltersResolver = app(CatalogFiltersResolver::class);
        $query = $request->all();
        $query['store_id'] = __hardcoded(1);
        $query['brands'] = '608';
        $filters = $catalogFiltersResolver->resolve($query);
        return $filters;
    }
}
