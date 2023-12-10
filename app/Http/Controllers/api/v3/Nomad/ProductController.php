<?php

namespace App\Http\Controllers\api\v3\Nomad;

use App\Actions\Nomad\RetrieveAvailableNomadFiltersAction;
use App\DTO\Nomad\NomadCatalogQueryDTO;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $params = $request->all();
        return [];
    }

    private function buildParams()
    {

    }

    public function getNomadFilters(Request $request, RetrieveAvailableNomadFiltersAction $action)
    {
        $query = $request->all();
        $store_id = __hardcoded(1);
        return $action->handle(new NomadCatalogQueryDTO($query + ['store_id' => $store_id]));
    }
}
