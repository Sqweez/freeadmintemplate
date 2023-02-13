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
        $message .= sprintf('Сумма заказа %s тенге', number_format($finalPrice, 0, ' ', ' '));
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
            $byCategory = number_format(100 * $total / ($finalPrice + $sale->balance), 2);
            $message .= sprintf('Категория %s - %s', $marginType['title'], $byCategory) . "%";
            $message .= "\n";
        }
        $message .= "<a href='". $sale->getReportURL() ."'>Ссылка на заказ</a>";
        $message .= "\n";
        $message .= "<a href='". $sale->getCancelURL() ."'>Отменить заказ❌</a>";
        $message .= "\n";
        $message .= "<a href='". $sale->getConfirmationURL() ."'>Подтвердить заказ✔</a>";
        $message .= "\n";
        return urlencode($message);
    }
}
