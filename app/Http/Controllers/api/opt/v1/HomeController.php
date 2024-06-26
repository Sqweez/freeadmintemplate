<?php

namespace App\Http\Controllers\api\opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\ProductResource;
use App\Resolvers\Opt\DailyProductResolver;
use App\Resolvers\Opt\NoveltyProductResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends BaseApiController
{
    public function __invoke(Request $request, NoveltyProductResolver $noveltyProductResolver, DailyProductResolver $dailyProductResolver): JsonResponse
    {
        return $this->respondSuccessNoReport([
            'banners' => [],
            'daily' => $dailyProductResolver->resolve(),
            'novelties' => ProductResource::collection(
                $noveltyProductResolver->resolve()->get()
            ),
        ]);
    }
}
