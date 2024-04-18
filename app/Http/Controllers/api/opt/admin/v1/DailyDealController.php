<?php

namespace App\Http\Controllers\api\opt\admin\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\DailyDeal\AdminDailyDealResource;
use App\v2\Models\OptDailyDeal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyDealController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->respondSuccess([
            'deals' => AdminDailyDealResource::collection(
                OptDailyDeal::query()
                    ->withCount('items')
                    ->latest()
                    ->get()
            )
        ]);
    }

    public function create(Request $request)
    {
        $deal = OptDailyDeal::create([
            'active_from' => Carbon::parse($request->active_from),
            'active_to' => Carbon::parse($request->active_to),
        ]);
        foreach ($request->get('products') as $item) {
            $deal->items()->create($item);
        }
        return $this->respondSuccess([
            'message' => 'Успешно создана'
        ]);
    }

    /**
     * @throws \Exception
     */
    public function delete(OptDailyDeal $deal): \Illuminate\Http\JsonResponse
    {
        $deal->delete();
        return $this->respondSuccess();
    }
}
