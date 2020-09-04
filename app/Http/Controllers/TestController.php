<?php

namespace App\Http\Controllers;

use App\CategoryProduct;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ReportResource;
use App\Product;
use App\Sale;
use Carbon\Carbon;
use Barryvdh\Debugbar;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request) {

        $FILTERS = [
            'ALL_TIME' => 1,
            'CURRENT_MONTH' => 2,
            'TODAY' => 3,
            'CUSTOM_FILTER' => 4,
            'LAST_3_DAYS' => 5,
        ];

        $filter = intval($request->get('filter')) ?? 3;
        $start = $request->get('start') ?? null;
        $finish = $request->get('finish') ?? null;

        $dates = [];

        $currentDate = Carbon::today();
        switch ($filter) {
            case $FILTERS['TODAY']: {
                $dates = [
                    $currentDate->toDateString(),
                ];
                break;
            }
            case $FILTERS['CURRENT_MONTH']: {
                $dates = [
                    $currentDate->subDays(30)->toDateString(),
                ];
                break;
            }
            case $FILTERS['ALL_TIME']: {
                $dates = [
                    Carbon::createFromTimestamp(0)->toDateString()
                ];
                break;
            }

            case $FILTERS['LAST_3_DAYS']: {
                $dates = [
                    $currentDate->subDays(3)->toDateString()
                ];
                break;
            }

            case $FILTERS['CUSTOM_FILTER']: {
                $dates = [
                    Carbon::parse($start),
                    Carbon::parse($finish)
                ];
                break;
            }

            default: {
                $dates = [
                    Carbon::now()->toDateString()
                ];
            }
        }

        if (count($dates) === 1) {
            $dates[] = Carbon::now()->toDateString();
        }

        return
        view('test', [
            'reports' => ReportResource::collection(
                Sale::with(['client', 'user', 'store', 'products'])->whereDate('created_at', '>=', $dates[0])
                    ->whereDate('created_at', '<=', $dates[1])
                    ->orderBy('created_at', 'desc')
                    ->get())->toArray($request)
        ]);
    }
}
