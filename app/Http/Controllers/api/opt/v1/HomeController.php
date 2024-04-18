<?php

namespace App\Http\Controllers\api\opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\DailyDeal\AdminDailyDealResource;
use App\Http\Resources\Opt\ProductResource;
use App\Resolvers\Opt\DailyProductResolver;
use App\Resolvers\Opt\NoveltyProductResolver;
use App\v2\Models\OptDailyDeal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends BaseApiController
{
    public function __invoke(Request $request, NoveltyProductResolver $noveltyProductResolver, DailyProductResolver $dailyProductResolver): JsonResponse
    {
        $dailyDeal = OptDailyDeal::query()
            ->where('active_from', '<=', now())
            ->where('active_to', '>=', now())
            ->withCount('items')
            ->first();
        return $this->respondSuccessNoReport([
            'banners' => [],
            'daily' => [
                'deal' => new AdminDailyDealResource($dailyDeal),
                'products' => ProductResource::collection(
                    $dailyProductResolver->resolve()->get()
                ),
            ],
            'novelties' => ProductResource::collection(
                $noveltyProductResolver->resolve()->get()
            ),
        ]);
    }
}
