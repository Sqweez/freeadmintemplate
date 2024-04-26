<?php

namespace App\Repository\Opt;

use App\v2\Models\OptDailyDeal;
use App\v2\Models\OptDailyDealProduct;
use Illuminate\Support\Collection;

class DailyProductsRepository
{
    public function query()
    {
        return OptDailyDeal::query()
            ->where('active_from', '<=', now())
            ->where('active_to', '>=', now());
    }

    public function getProductIds(): Collection
    {
        $deals = $this->query()->pluck('id');
        return OptDailyDealProduct::query()
            ->where('opt_daily_deal_id', $deals->toArray())
            ->get();
    }
}
