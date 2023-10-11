<?php

namespace App\Actions\Reports;

use App\Concerns\UseQuickBindings;
use App\Resolvers\ProductSku\ProductSkuNameResolver;
use App\Sale;

class CollectProductReportAction {

    use UseQuickBindings;

    public function handle($from, $to, $products) {
        $sales = Sale::query()
            ->reportDate([$from, $to])
            ->whereHas('products', function ($query) use ($products) {
                return $query->whereIn('product_id', $products);
            })
            ->with(['products' => function ($query) use ($products) {
                return $query
                    ->whereIn('product_id', $products)
                    ->with('product.product.attributes')
                    ->with('product.attributes');
            }])
            ->with('store:id,name')
            ->get();

        return $this->_transformSales($sales);
    }

    private function _transformSales ($sales) {
        $byStores =  collect($sales)
            ->groupBy('store_id')
            ->map(function ($items, $key) {

                $products = $items->pluck('products')->flatten(1);
                $totalFinalPrice = $products->reduce(function ($a, $c) {
                    return $a + $c->final_price;
                }, 0);
                $totalPurchasePrice = $products->reduce(function ($a, $c) {
                    return $a + $c->purchase_price;
                }, 0);

                $products = $products
                    ->groupBy('product_id')
                    ->map(function ($products, $product_id) {
                        $productSku = $products->first()['product'];
                        return [
                            'id' => $product_id,
                            'count' => $products->count(),
                            'name' => ProductSkuNameResolver::i()->resolve($productSku),
                            'total_purchase' => $products->reduce(function ($a, $c) {
                                return $a + $c->purchase_price;
                            }, 0),
                            'total_final' => $products->reduce(function ($a, $c) {
                                return $a + $c->final_price;
                            })
                        ];
                    })
                    ->values();

                return [
                    'store' => $items->first()['store'],
                    'products' => $products,
                    'total_final' => $totalFinalPrice,
                    'total_purchase' => $totalPurchasePrice,
                ];
            })
            ->values();

        $total = collect([[
            'store' => [
                'name' => 'Итого',
                'id' => -1
            ],
            'total_final' => $byStores->reduce(function ($a, $c) {
                return $a + $c['total_final'];
            }, 0),
            'total_purchase' => $byStores->reduce(function ($a, $c) {
                return $a + $c['total_purchase'];
            }, 0),
            'products' => $byStores->pluck('products')
                ->flatten(1)
                ->groupBy('id')
                ->map(function ($products, $id) {
                    return [
                        'id' => $id,
                        'name' => $products->first()['name'],
                        'count' => $products->reduce(function ($a, $c) {
                            return $a + $c['count'];
                        }, 0),
                        'total_purchase' => $products->reduce(function ($a, $c) {
                            return $a + $c['total_purchase'];
                        }, 0),
                        'total_final' => $products->reduce(function ($a, $c) {
                            return $a + $c['total_final'];
                        }, 0),
                    ];
                })
                ->values(),
        ]]);

        return $byStores->mergeRecursive($total);
    }
}
