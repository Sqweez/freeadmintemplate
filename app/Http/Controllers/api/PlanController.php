<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Plan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Builder[]|Collection
     */
    public function index()
    {
        return Plan::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Plan[]|Collection
     */
    public function store(Request $request)
    {
        $plans = $request->all();
        Plan::truncate();
        foreach ($plans as $plan) {
            Plan::create([
                'store_id' => $plan['store_id'],
                'week_plan' => $plan['week_plan'],
                'month_plan' => $plan['month_plan'],
                'prize' => $plan['prize']
            ]);
        }

        return Plan::all();
    }
}
