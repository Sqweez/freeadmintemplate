<?php

namespace App\Repository;

use App\Client;
use App\Promocode;
use App\Sale;
use App\Store;

class PromocodeRepository
{
    public function getAvailableStocks(Client $client, Store $store)
    {
        $user = auth()->user();
        $stocks = Promocode::query()
            ->where('promocode_apply_type_id', __hardcoded(2))
            ->where(function ($query) use ($client) {
                return $query
                    ->where('apply_to_clients_id', __hardcoded(1))
                    ->orWhere(function ($query) use ($client) {
                        return $query->whereHas('client_promocodes', function ($subQuery) use ($client) {
                            return $subQuery->where('client_id', $client->id)
                                ->orWhere('card_code', $client->client_card);
                        });
                    });
            })
            ->where('is_active', true)
            ->get();

        $stocks = $stocks->filter(function (Promocode $stock) use ($store) {
            return is_null($stock->available_stores) || in_array($store->id, $stock->available_stores);
        });

        $sales = Sale::query()
            ->whereIn('promocode_id', $stocks->pluck('id'))
            ->select(['id', 'promocode_id', 'client_id'])
            ->get();

        return $stocks
            ->filter(function (Promocode $stock) use ($sales, $client) {
                $needleSales = collect($sales)->where('promocode_id', $stock->id);
                $clientCount = $needleSales->where('client_id', $client->id);
                return (is_null($stock->total_use_quantity) || $stock->total_use_quantity > $needleSales->count())
                    &&
                    (is_null($stock->per_client_use_quantity) || $stock->per_client_use_quantity > $clientCount->count());
            });
    }
}
