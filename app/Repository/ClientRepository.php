<?php

namespace App\Repository;

use App\Client;
use App\DTO\Filters\ClientFilterDTO;

class ClientRepository
{
    public function query(ClientFilterDTO $filters)
    {
        return Client::query()
            ->orderByDesc('created_at')
            ->with([/*'sales', 'transactions', */'city', 'loyalty', 'latest_gift_giveaway'])
            /*->when($filters->wholesales, function ($query) {
                return $query
                    ->where('is_wholesale_buyer', true)
                    ->orderBy('wholesale_status');
            })*/
            ->tap(function ($query) use ($filters) {
                return $this->search($query, $filters->search);
            })
            /*->when($filters->partner !== null, function ($query) {
                return $query
                    ->where('is_partner', true);
            })*/
            /*  ->when($filters->is_partner !== null, function ($query) use ($filters) {
                  return $query->where('is_partner', $filters->is_partner);
              })*/

            ->when($filters->loyalty_id !== null, function ($query) use ($filters) {
                return $query->where('loyalty_id', $filters->loyalty_id);
            })
            ->when($filters->gender !== null, function ($query) use ($filters) {
                return $query->where('gender', $filters->gender);
            })
            ->when($filters->client_city !== null, function ($query) use ($filters) {
                return $query->where('client_city', $filters->client_city);
            })
           /* ->when($filters->is_wholesale_buyer !== null, function ($query) use ($filters) {
                return $query->where('is_wholesale_buyer', $filters->is_wholesale_buyer);
            })*/
            ->when($filters->is_kaspi !== null, function ($query) use ($filters) {
                return $query->where('is_kaspi', $filters->is_kaspi);
            })
            ->with(['barter_balance' => function ($query) {
                return $query->where('is_active', true);
            }]);
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
