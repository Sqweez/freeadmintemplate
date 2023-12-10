<?php

namespace App\Actions\Nomad;

use App\Attribute;

class RetrieveAvailableNomadFiltersAction
{

    public function handle($productIds)
    {
        return Attribute::query()
            ->with(['values' => function ($query) use ($productIds) {
                return $query
                    ->whereHas('products', function ($q) use ($productIds) {
                        return $q
                            ->where('filterable', true)
                            ->whereIn('products.id', $productIds);
                    });
            }])
            ->get()
            ->filter(function (Attribute $attribute) {
                // Исключаем пустые фильтры, а также фильтры помеченные как "Ярлыки"
                return count($attribute->values) && $attribute->id !== __hardcoded(14);
            })
            ->values();
    }
}
