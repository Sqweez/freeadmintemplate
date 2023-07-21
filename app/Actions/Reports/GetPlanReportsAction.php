<?php

namespace App\Actions\Reports;

use App\Plan;
use App\Sale;
use App\Store;
use Carbon\Carbon;

class GetPlanReportsAction {

    private CalculateSaleTotalAmount $calculateTotal;

    public function __construct(CalculateSaleTotalAmount $calculateSaleTotalAmount) {
        $this->calculateTotal = $calculateSaleTotalAmount;
    }

    public function handle($store_id): array {
        $today = now();
        $startOfMonth = $today->startOf('month')->toDateString();
        $plan = Plan::where('store_id', $store_id)->first();

        if (!$plan) {
            $plan = new Plan();
            $plan->month_plan = 0;
            $plan->week_plan = 0;
        }

        $monthlySales = Sale::whereDate('created_at', '>=', $startOfMonth)
            ->where('store_id', $store_id)
            ->where('user_id', '!=', 2)
            ->where('payment_type', '!=', 4)
            ->with(['products:product_price,discount,sale_id'])
            ->select(['id', 'store_id', 'kaspi_red', 'balance', 'created_at'])
            ->get();


        $weeklySales = $monthlySales->filter(function ($i){
            return Carbon::parse($i->created_at)->greaterThanOrEqualTo(now()->startOfWeek());
        });

        $todaySales = $monthlySales->filter(function ($i) {
            return Carbon::parse($i->created_at)->greaterThanOrEqualTo(now()->startOfDay());
        });

        return [
            'store_name' => Store::find($store_id)->name,
            'today' => $this->calculateDailyPlanExecution($plan, $todaySales),
            'week' => $this->calculateDailyPlanExecution($plan, $weeklySales),
            'month' => $this->calculateMonthlyPlanExecution($plan, $monthlySales),
            'plan' => $plan
        ];
    }

    private function calculateDailyPlanExecution (Plan $plan, $sales): array {
        $amount = $this->calculateTotal->handle($sales);
        $planAmount = ceil($plan->month_plan / now()->daysInMonth);
        return [
            'amount' => $amount,
            'plan' => $planAmount,
            'percent' => floor(100 * $amount / $planAmount)
        ];
    }

    private function calculateWeeklyPlanExecution (Plan $plan, $sales): array {
        $amount = $this->calculateTotal->handle($sales);
        $planAmount = $plan->week_plan;
        return [
            'amount' => $amount,
            'plan' => $planAmount,
            'percent' => floor(100 * $amount / $planAmount)
        ];
    }

    private function calculateMonthlyPlanExecution (Plan $plan, $sales): array {
        $amount = $this->calculateTotal->handle($sales);
        $planAmount = $plan->month_plan;
        $percent = floor(100 * $amount / $planAmount);
        return [
            'amount' => $amount,
            'plan' => $planAmount,
            'percent' => $percent,
            'got_prize' => $percent > 100,
        ];
    }
}
