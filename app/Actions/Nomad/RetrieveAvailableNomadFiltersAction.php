<?php

namespace App\Actions\Nomad;

use App\Attribute;

class RetrieveAvailableNomadFiltersAction
{

    public function handle($productIds)
    {
        $attributes = Attribute::query()
            ->with(['values' => function ($query) use ($productIds) {
                return $query
                    ->whereHas('products', function ($q) use ($productIds) {
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
