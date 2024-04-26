<?php

namespace App\Resolvers\Opt;

use App\Http\Resources\Opt\DailyDeal\AdminDailyDealResource;
use App\Http\Resources\Opt\ProductResource;
use App\Repository\Opt\DailyProductsRepository;

class DailyProductResolver extends BaseProductResolver
{
    public function resolve(): ?array
    {
        $dailyDealProductsRepository = app(DailyProductsRepository::class);
        $dailyDeal = $dailyDealProductsRepository->query()->first();
        if (!$dailyDeal) {
            return null;
        }
        return [
            'deal' => new AdminDailyDealResource($dailyDeal),
            'products' => ProductResource::collection(
                $this
                    ->getBaseProductQuery(
                        $this->retrieveCurrency(null),
                        $this->getWholesaleStoreIds()
                    )
                    ->whereHas('optDailyDeals', function ($query) {
                        return $query->whereHas('dailyDeal', function ($subQuery) {
                            $subQuery->where('active_from', '<=', now())
                                ->where('active_to', '>=', now());
                        });
                    })
                    ->with(['optDailyDeals' => function ($query) {
                        return $query->whereHas('dailyDeal', function ($subQuery) {
                            $subQuery->where('active_from', '<=', now())
                                ->where('active_to', '>=', now());
                        });
                    }])
                    ->get()
            ),
        ];
    }
}
