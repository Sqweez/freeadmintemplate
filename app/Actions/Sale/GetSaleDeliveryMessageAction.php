<?php

namespace App\Actions\Sale;

use App\Http\Resources\v2\Report\ReportsResource;
use App\Sale;

class GetSaleDeliveryMessageAction {

    public function handle(Sale $sale): string {
        $sale = new ReportsResource($sale);
        $message = 'Новая доставка №' . $sale->id . "\n";
        $message .= 'Точка выдачи: ' . $sale->store->name . "\n";
        $message .= 'ФИО: ' . $sale['client']['client_name'] . "\n";
        $message .= 'Телефон: ' . $sale['client']['client_phone'] . "\n";
        $message .= $sale['comment'] . "\n";
        $message .= $sale['is_paid'] ? 'Оплачен ✅✅✅' : 'НЕ ОПЛАЧЕН ❌❌❌';
        $message .= "\n";
        $message .= 'Способ оплаты: ' . Sale::PAYMENT_TYPES[$sale->payment_type]['name'] . "\n";
        $message .= "Товары: \n";
        $saleProducts = $sale['products'];
        $products = collect($sale['products'])
            ->groupBy('product_id')
            ->map(function ($item) {
                return collect($item)->first();
            })
            ->values()
            ->all();
        foreach ($products as $key => $product) {
            $_product = $product['product']['product'];
            $attributes = collect($product['product']['product']['attributes'])
                ->mergeRecursive(collect($product['product']['attributes']))
                ->pluck('attribute_value')
                ->join(', ');
            $count = $saleProducts->filter(function ($item) use ($product) {
                return $item['product_id'] === $product['product_id'];
            })->count();
            $message .=
                ($key + 1) . '. ' .
                $_product['product_name'] . ' ' . $attributes .
                ' | ' . $count . 'шт' .
                "\n";
        }
        $message .= 'К оплате: ' . $sale->final_price_without_red . 'тнг';
        return $message;
    }
}
