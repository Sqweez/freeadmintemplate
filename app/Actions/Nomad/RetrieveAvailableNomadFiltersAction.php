<?php

namespace App\Actions\Nomad;

use App\Attribute;
use App\DTO\Nomad\NomadCatalogQueryDTO;

class RetrieveAvailableNomadFiltersAction
{

    public function handle(NomadCatalogQueryDTO $catalogQueryDTO)
    {
        $attributes = Attribute::query()
            ->with(['values' => function ($query) {
                return $query
                    ->has('products')
                    ->with('products');
            }])
            ->get();

        return $attributes;
    }
}
