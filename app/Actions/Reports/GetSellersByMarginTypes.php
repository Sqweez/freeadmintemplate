<?php

namespace App\Actions\Reports;

use App\Sale;

class GetSellersByMarginTypes {

    public function handle($start, $finish, $margin_type = -1) {
        return Sale::query()
            ->physicalSales()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $finish)
            ->when($margin_type !== -1, function ($query) use ($margin_type) {
                return $query->whereHas('products', function ($query) use ($margin_type) {
                    return $query->whereHas('product', function ($subQuery) use ($margin_type) {
                        return $subQuery->where('margin_type_id', $margin_type);
                    });
                });
            })
            /*->when($margin_type !== -1, function ($query) use ($margin_type) {
                return $query->with(['products' => function ($query) use ($margin_type) {
                    return $query->with(['product' => function ($subQuery) use ($margin_type) {
                        return $subQuery->where('margin_type_id', $margin_type);
                    }]);
                }]);
            })
            ->when($margin_type === -1, function ($q) {
                return $q->with('products.product');
            })*/
            ->with('products.product')
            ->with('user')
            ->with('store')
            ->get()
            ->map(function ($sales) use ($margin_type) {
                if ($margin_type === -1) {
                    return $sales;
                }
                $sales->products = $sales->products->filter(function ($product) use ($margin_type) {
                    return $product->product->margin_type_id === $margin_type;
                });
                return $sales;
            })
            ->groupBy('user_id')
            ->map(function ($sales, $user_id) {
                return [
                    'user_id' => $user_id,
                    'user' => $sales->first()->user->name,
                    'store' => $sales->first()->store->name,
                    'total' => $sales->reduce(function ($a, $c) {
                        return $a + $c->products->reduce(function ($_a, $_c) {
                            return $_a + $_c->final_price;
                            }, 0);
                    }, 0)
                ];
            })
            ->values()
            ->sortByDesc('total')
            ->take(10)
            ->values();
    }
}
