<?php

namespace App\Http\Controllers\api\v2;

use App\Actions\Dashboard\GetMarginTotalSalesByUser;
use App\Http\Controllers\api\BaseApiController;
use App\Resolvers\User\UserIdResolver;
use Illuminate\Http\JsonResponse;

class DashboardController extends BaseApiController
{
    public function getMyMonthlyMarginSales(): JsonResponse
    {
        $userId = UserIdResolver::resolve();
        $from = today()->startOfMonth();
        $to = now();

        return $this->respondSuccess([
            'items' => GetMarginTotalSalesByUser::i()->handle($userId, $from, $to),
        ]);
    }
}
