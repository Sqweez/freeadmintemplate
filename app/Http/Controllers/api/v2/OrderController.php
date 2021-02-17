<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\api\CartController;
use App\Http\Controllers\Controller;
use App\Http\Resources\v2\Order\OrderResource;
use App\Order;
use App\ProductBatch;
use App\Sale;
use App\SaleProduct;
use App\User;
use TelegramService;

class OrderController extends Controller
{

    public function getOrders() {
        /*$orders = Order::all();
        $orders_with_city_text = $orders->filter(function ($order) {
            return strlen($order['city']) > 1;
        })->each(function ($order) {
            $cityId = null;
            if ($order['city'] === 'Аксу') {
                $cityId = 11;
            }
            if ($order['city'] === 'Павлодар') {
                $cityId = 9;
            }
            if ($order['city'] === 'Караганда') {
                $cityId = 5;
            }
            if ($order['city'] === 'Усть-Каменогорск') {
                $cityId = 12;
            }
            if ($order['city'] === 'Семей') {
                $cityId = 13;
            }
            if ($order['city'] === 'Экибастуз') {
                $cityId = 10;
            }
            if ($order['city'] === 'Риддер') {
                $cityId = 24;
            }
            if ($cityId !== null) {
                Order::find($order['id'])->update(['city' => $cityId]);
            } else {
                Order::find($order['id'])->update(['city' => 25]);
            }
        });
        $orders_with_city_number = $orders->filter(function ($order) {
            return strlen($order['city']) === 1;
        })->each(function ($order) {
            $cityId = null;
            if (intval($order['city']) === 1) {
                $cityId = 9;
            }
            if (intval($order['city']) === 2) {
                $cityId = 13;
            }
            if (intval($order['city']) === 3) {
                $cityId = 12;
            }
            if (intval($order['city']) === 4) {
                $cityId = 10;
            }
            if (intval($order['city']) === 5) {
                $cityId = 5;
            }
            if (intval($order['city']) === 6) {
                $cityId = 25;
            }
            Order::find($order['id'])->update(['city' => $cityId]);
        });*/
        return OrderResource::collection(Order::with(
            [
                'store:id,name', 'items',
                'items.product', 'items.product.attributes',
                'items.product.product', 'items.product.product.attributes',
                'items.product.product.manufacturer',
            ]
        )->orderByDesc('created_at')->get());
    }

    public function deleteOrder(Order $order) {
        $order->delete();
    }

    public function accept(Order $order) {
        if ($order['status'] == 1) {
            return ['error' => 'Заказ уже выполнен!'];
        }

        if ($order['status'] == -1) {
            return ['error' => 'Заказ уже отменен!'];
        }


        $store_id = $order['store_id'];
        $products = $order->items;

        $sale = Sale::create([
            'client_id' => $order['client_id'],
            'store_id' => $store_id,
            'user_id' => User::IRON_WEB_STORE,
            'discount' => $order['discount'],
            'kaspi_red' => 0,
            'balance' => $order['balance'] ?? 0
        ]);


        $products->each(function ($product) use ($sale) {
            SaleProduct::create([
                'product_batch_id' => $product['product_batch_id'],
                'product_id' => $product['product_id'],
                'sale_id' => $sale['id'],
                'purchase_price' => $product['purchase_price'],
                'product_price' => $product['product_price'],
                'discount' => $sale['discount']
            ]);
        });

        (new CartController())->createClientSale($sale);

        $order->status = 1;

        $order->update();

        $message = 'Заказ №' . $order->id . ' выполнен 💪💪💪';

        TelegramService::sendMessage(env('TELEGRAM_KZ_CHAT_ID'), urlencode($message));

        return 'Заказ выполнен!';
    }

    public function decline(Order $order) {
        if ($order['status'] == 1) {
            return ['error' => 'Заказ уже выполнен!'];
        }

        if ($order['status'] == -1) {
            return ['error' => 'Заказ уже отменен!'];
        }

        $order->status = -1;

        $order->update();

        $products = $order->items;

        foreach ($products as $product) {
            $productBatch = ProductBatch::find($product['product_batch_id']);
            $productBatch->quantity = $productBatch->quantity + 1;
            $productBatch->update();
        }

        $message = 'Заказ №' . $order->id . ' отменен 😠😠😠';

        TelegramService::sendMessage(env('TELEGRAM_KZ_CHAT_ID'), urlencode($message));

        return 'Заказ отменен!';

    }
}
