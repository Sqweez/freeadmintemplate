<?php

namespace App\Actions\Reports;

use App\Sale;

class GetSellersByPaymentTypeAction {

    public function handle($start, $finish, $payment_type = -1) {
        return Sale::query()
            ->physicalSales()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $finish)
            ->when($payment_type !== -1, function ($q) use ($payment_type) {
                return $q->where('payment_type', $payment_type);
            })
            ->with(['user', 'store'])
            ->get()
            ->groupBy('user_id')
            ->map(function ($sales, $user_id) {
                return [
                    'total' => $sales->reduce(function ($a, $c) {
                        return $a + $c->final_price;
                    }, 0),
                    'user' => $sales->first()->user->name,
                    'store' => $sales->first()->store->name,
                    'user_id' => $user_id
                ];
            })
            ->values()
            ->sortByDesc('total')
            ->take(10)
            ->values();
    }
}
