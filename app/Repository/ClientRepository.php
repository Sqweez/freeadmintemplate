<?php

namespace App\Repository;

use App\Client;
use App\DTO\Filters\ClientFilterDTO;
use Illuminate\Database\Eloquent\Builder;

class ClientRepository
{
    public function query(ClientFilterDTO $filters): Builder
    {
        return $this->filteredQuery($filters)
            ->with([/*'sales', 'transactions', */'city', 'loyalty', 'latest_gift_giveaway'])
            ->with(['barter_balance' => function ($query) {
                return $query->where('is_active', true);
            }]);
    }

    public function queryForExport(ClientFilterDTO $filters): Builder
    {
        return $this->filteredQuery($filters)
            ->select(['id', 'client_name', 'client_phone', 'client_city', 'cached_total_sale_amount'])
            ->with('city');
    }

    private function filteredQuery(ClientFilterDTO $filters): Builder
    {
        return Client::query()
            ->orderByDesc('created_at')
            ->tap(function ($query) use ($filters) {
                return $this->search($query, $filters->search);
            })
            ->when($filters->is_partner !== null, function ($query) use ($filters) {
                return $query->where('is_partner', $filters->is_partner);
            })
            ->when($filters->loyalty_id !== null, function ($query) use ($filters) {
                return $query->where('loyalty_id', $filters->loyalty_id);
            })
            ->when($filters->gender !== null, function ($query) use ($filters) {
                return $query->where('gender', $filters->gender);
            })
            ->when($filters->client_city !== null, function ($query) use ($filters) {
                return $query->where('client_city', $filters->client_city);
            })
            ->when($filters->without_code === true, function ($query) {
                return $query->where(function ($query) {
                    return $query
                        ->whereNull('client_card')
                        ->orWhereRaw('LENGTH(client_card) < 5');
                });
            })
            ->when($filters->is_wholesale_buyer === true, function ($query) {
                return $query->where('is_wholesale_buyer', true);
            })
            ->when($filters->is_kaspi === true, function ($query) {
                return $query->where('is_kaspi', true);
            });
    }

    public function search($query, $search)
    {
        return $query->when($search, function ($subQuery) use ($search) {
            return $subQuery->where(function ($query) use ($search) {
                $query->where('client_name', 'like', '%' . $search . '%')
                    ->orWhere('client_card', $search)
                    ->orWhere('client_phone', 'like', '%' . $search . '%');
            });
        });
    }
}
