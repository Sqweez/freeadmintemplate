<?php

namespace App\Resolvers\Opt;

class DailyProductResolver extends BaseProductResolver
{
    public function resolve()
    {
        return $this
            ->getBaseProductQuery(
                $this->retrieveCurrency(null),
                $this->getWholesaleStoreIds()
            )
            ->whereHas('optDailyDeals', function ($query) {
                return $query->whereHas('dailyDeal', function ($subQuery) {
                    $subQuery->where('active_from', '<=', now())
                        ->where('active_to', '>=', now());
                });
            });
    }
}
