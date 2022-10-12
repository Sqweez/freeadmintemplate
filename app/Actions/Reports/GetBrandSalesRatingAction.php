<?php

namespace App\Actions\Reports;

use App\Sale;

class GetBrandSalesRatingAction {

    private CalculateSaleTotalAmount $calculationService;

    public function __construct(CalculateSaleTotalAmount $calculationService) {
        $this->calculationService = $calculationService;
    }

    public function handle($start, $finish, $store_id) {
        return Sale::query()
            ->physicalSales()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $finish)
            ->when($store_id !== -1, function ($q) use ($store_id) {
                return $q->where('store_id', $store_id);
            })
            ->with('products.product.product.manufacturer')
            ->get()
            ->map(function ($sale) {
                $sale->products->map(function ($product) {
                    $product['final_price'] = $product->final_price;
                    $product['manufacturer_id'] = $product->product->product->manufacturer_id;
                });
                return $sale;
            })
            ->pluck('products')
            ->flatten()
            ->groupBy('manufacturer_id')
            ->map(function ($products, $manufacturer_id) {
                $total = collect($products)->reduce(function ($a, $c) {
                    return $a + $c['final_price'];
                }, 0);
                return [
                    'total' => $total,
                    'manufacturer' => $products->first()->product->product->manufacturer->manufacturer_name,
                    'manufacturer_id' => $manufacturer_id
                ];
            })
            ->values()
            ->sortByDesc('total')
            ->take(10)
            ->values();
    }
}
