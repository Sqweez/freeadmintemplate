<?php

namespace App\Repository;

use App\DTO\Filters\TransferFilterDTO;
use App\Store;
use App\Transfer;
use App\User;

class TransferRepository
{
    public function get(TransferFilterDTO $filter, ?User $user) {
        return Transfer::query()
            ->with(['parent_store:id,name', 'child_store:id,name', 'companionSale'])
            ->with(['user:id,name'])
            ->with(['batches' => function ($query) {
                return $query
                    ->with('productBatch:id,purchase_price')
                    ->with('product:id,product_id,self_price')
                    ->with('product.product:id,product_price');
            }])
            ->select(['id', 'parent_store_id', 'child_store_id', 'user_id', 'photos', 'created_at', 'updated_at', 'is_confirmed', 'is_accepted'])
            ->when($filter->search, function ($query) use ($filter) {
                return $query
                    ->whereHas('batches', function ($query) use ($filter) {
                        return $query->whereHas('product', function ($query) use ($filter) {
                            return $query->whereHas('product', function ($query) use ($filter) {
                                return $query->where('product_name', 'LIKE', '%' . $filter->search . '%');
                            });
                        });
                    });
            })
            ->when($filter->mode === 'current', function ($query) {
                return $query
                    ->where('is_confirmed', false)
                    ->where('is_accepted', true);
            })
            ->when($filter->mode === 'history', function ($query) {
                return $query
                    ->where('is_confirmed', true)
                    ->where('is_accepted', true);
            })
            ->when($filter->mode === 'not_accepted', function ($query) {
                return $query
                    ->where('is_accepted', false);
            })
            ->when($filter->is_partner, function ($q) {
                return $q->whereHas('child_store', function ($q) {
                    return $q->where('type_id', Transfer::PARTNER_SELLER_ID);
                });
            })
            ->when($user && $user->isSeller(), function ($q) use ($user) {
                return $q
                    ->where(function ($q) use ($user) {
                        return $q->where('child_store_id', $user->store_id)
                            ->orWhere('parent_store_id', $user->store_id);
                    });
            })
            ->when($user && $user->isFranchise(), function ($q) use ($user) {
                $store = Store::find($user->store_id);
                $city_id = $store->city_id;
                $store_ids = Store::where('city_id', $city_id)->get()->pluck('id');
                return $q
                    ->where(function ($q) use ($store_ids) {
                        return $q->whereIn('child_store_id', $store_ids->toArray())
                            ->orWhereIn('parent_store_id', $store_ids->toArray());
                    });
            })
            ->when($filter->parent_store_id, function ($query) use ($filter) {
                return $query->where('parent_store_id', $filter->parent_store_id);
            })
            ->when($filter->child_store_id, function ($query) use ($filter) {
                return $query->where('child_store_id', $filter->child_store_id);
            })
            ->when($filter->created_at_min, function ($query) use ($filter) {
                return $query->where('created_at', '>=', $filter->created_at_min);
            })
            ->when($filter->created_at_max, function ($query) use ($filter) {
                return $query->where('created_at', '<=', $filter->created_at_max);
            })
            ->when($filter->updated_at_min, function ($query) use ($filter) {
                return $query->where('updated_at', '>=', $filter->updated_at_min);
            })
            ->when($filter->updated_at_max, function ($query) use ($filter) {
                return $query->where('updated_at', '<=', $filter->updated_at_max);
            })
            ->orderByDesc('id');
    }
}
