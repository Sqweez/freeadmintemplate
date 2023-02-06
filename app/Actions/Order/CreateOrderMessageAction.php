<?php

namespace App\Actions\Order;

use App\Order;
use App\ProductBatch;
use App\v2\Models\ProductSku;

class CreateOrderMessageAction {

    public function handle(Order $order): string {
        $message = 'Новый' . ($order->is_iherb ? ' IHERB' : '') . ' заказ 💪💪💪' . "\n";
        $message .= 'Заказ №' . $order['id'] . "\n";
        $message .= 'ФИО: ' . $order['fullname'] . "\n";
        $message .= 'Номер телефона: ' . $order['phone'] . "\n";
        $message .= 'Город: ' . $order->city_text->name . "\n";
        $message .= 'Адрес: ' . $order['address'] . "\n";

        $discount = $order['discount'];

        $products = ProductSku::with(['product', 'product.attributes', 'attributes', 'product.manufacturer'])->whereIn('id', $order->items->pluck('product_id'))->get();
        $cartProducts = collect($order->items);

        foreach ($products as $key => $product) {
            $attributes = $product->attributes->reduce(function ($a, $c) {
                return $c['attribute_value'] . ', ' . $a;
            }, '');

            $_cartProducts = $cartProducts->filter(function ($i) use ($product) {
                return $i['product_id'] == $product['id'];
            });
            $count = $_cartProducts->count();
            $batches = ProductBatch::with('store')->whereIn('id', $_cartProducts->pluck('product_batch_id'))->get();
            $productFullName = sprintf(
                "%s. %s, %s, %s %sтг | %sшт",
                ($key + 1),
                $product->product_name,
                $product->manufacturer->manufacturer_name,
                $attributes,
                $product['product']['stock_price'],
                $count
            );

            $message .= sprintf(
                "<a href='%s'>%s</a>",
                'https://iron-addicts.kz/product/' . \Str::slug($product->product_name) . '/' . $product->product_id,
                $productFullName
            );

            $message .= "\n";
            //$message .= ($key + 1) . '.' . $product->product_name . ',' . $attributes . ' ' . $product['product']['stock_price'] . 'тг' . ' | ' . $count . 'шт.' . "\n";
            $message .= 'Склады товаров: ' . $batches->reduce(function ($a, $c) {
                    return $a . ' ' . $c['store']['name'] . ',';
                }, '') . "\n";
        }

        if (intval($discount) > 0) {
            $message .= 'Скидка: ' . $discount . '%' . "\n";
        }

        if (intval($order['balance']) > 0) {
            $message .= 'Списано с баланса: ' . $order['balance'] . ' тнг' . "\n";
        }

        if ($order['comment']) {
            $message .= 'Комментарий:' . $order['comment'] . "\n";
        }

        $delivery = Order::ORDER_DELIVERY[$order['delivery']]['text'];
        $payment = 'Оплата наличными';

        /* if ($order['delivery'] == 1) {
             $delivery = 'Самовывоз';
         }*/

        if ($order['payment'] == 1) {
            $payment = 'Оплата картой';
        }

        if ($order['payment'] == 2) {
            if ($order['is_paid']) {
                $payment = 'Онлайн оплата: ОПЛАЧЕНО!';
            } else {
                $payment = 'Онлайн оплата: НЕ ОПЛАЧЕНО!';
            }
        }

        $message .= 'Способ оплаты: ' . $payment . "\n";
        $message .= 'Способ получения: ' . $delivery . "\n";

        $totalCostWithDiscount = ceil($order->items->reduce(function ($a, $c) use ($discount){
                return $a + ($c['product_price'] * ((100 - intval($c['discount'])) / 100));
            }, 0) - intval($order['balance']));
        $totalCostWithDiscount = $totalCostWithDiscount - $order['promocode_fixed_amount'];
        $deliveryCost = $this->getDeliveryCost($order->city_text, $totalCostWithDiscount, $order['delivery']);

        $message .= 'Общая сумма: ' . $totalCostWithDiscount . 'тг' . "\n";
        $message .= 'Стоимость доставки: ' . $deliveryCost . 'тг' . "\n";
        $message .= 'Итого к оплате: ' . ($totalCostWithDiscount + $deliveryCost) . 'тг' . "\n";

        $message .= "<a href='https://ironadmin.ariesdev.kz/api/order/" . $order['id'] . "/decline'>Отменить заказ❌</a>" . "\n";
        $message .= "<a href='https://ironadmin.ariesdev.kz/api/order/" . $order['id'] . "/accept'>Заказ выполнен✔️</a>" . "\n";
        $message .= "<a href='https://ironadmin.ariesdev.kz/orders/" . $order['id'] . "/whatsapp'>Отправить в WA клиенту✔️</a>" . "\n";

        return urlencode($message);
    }

    private function getDeliveryCost($city, $total, $deliveryMethod): int {
        if ($deliveryMethod === 1) {
            return 0;
        }
        return $total - $city['delivery_threshold'] >= 0 ? 0 : $city['delivery_cost'];
    }
}
