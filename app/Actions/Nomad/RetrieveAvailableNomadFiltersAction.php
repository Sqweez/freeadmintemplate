<?php

namespace App\Actions\Nomad;

use App\Attribute;
use App\DTO\Nomad\NomadCatalogQueryDTO;

class RetrieveAvailableNomadFiltersAction
{

    public function handle(NomadCatalogQueryDTO $catalogQueryDTO)
    {
        $attributes = Attribute::query()
            ->with(['values' => function ($query) use ($catalogQueryDTO) {
                return $query
                    ->whereHas('products', function ($q) use ($catalogQueryDTO) {
                        return $q
                            ->where('filterable', true);
                    });
            }])
            ->get()
            ->filter(function (Attribute $attribute) {
                return count($attribute->values);
            })
            ->values();

        return $attributes;
    }
}
