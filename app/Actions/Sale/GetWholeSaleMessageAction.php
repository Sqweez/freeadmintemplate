<?php

namespace App\Actions\Sale;

use App\MarginType;
use App\Sale;
use App\SaleProduct;

class GetWholeSaleMessageAction {

    public function handle(Sale $sale): string {
        $sale->load('products.product.margin_type');
        $sale->load('client');
        $sale->load('store');
        $message = sprintf('Новая оптовая продажа №%s💪💪💪', $sale->id);
        $message .= "\n";
        $message .= sprintf('Клиент: %s', $sale->client->client_name);
        $message .= "\n";
        $message .= sprintf('Магазин: %s', $sale->store->name);
        $message .= "\n";
        $finalPrice = $sale->getFinalPriceAttribute();
        $message .= sprintf('Сумма заказа %s тенге', $finalPrice);
        $message .= "\n";
        $byMarginTypes = $sale->products
            ->map(function (SaleProduct $saleProduct) {
                $saleProduct['margin_type_id'] = $saleProduct->product->margin_type_id;
                return $saleProduct;
            })
            ->groupBy('margin_type_id')
            ->map(function ($products, $title) {
                return collect($products)->reduce(function ($a, $c) {
                    return $a + $c->final_sale_price;
                }, 0);
            })
            ->toArray();
        $marginTypes = MarginType::all();
        foreach ($marginTypes as $marginType) {
            $total = $byMarginTypes[$marginType['id']] ?? 0;
            $byCategory = number_format(100 * $total / $finalPrice, 2);
            $message .= sprintf('Категория %s - %s', $marginType['title'], $byCategory) . "%";
            $message .= "\n";
        }
        $message .= sprintf('<a href="%s">Ссылка на заказ</a>', $sale->getReportURL());
        $message .= "\n";
        $message .= sprintf('<a href="%s">Отменить заказ❌</a>', $sale->getCancelURL());
        $message .= "\n";
        $message .= sprintf('<a href="%s">Подтвердить заказ✔</a>', $sale->getConfirmationURL());
        return $message;
    }
}
