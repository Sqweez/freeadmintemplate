<?php

namespace App\Http\Controllers\api;

use App\Cart;
use App\CartProduct;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\TelegramService;
use App\Http\Resources\shop\CartResource;
use App\Order;
use App\OrderProduct;
use App\Product;
use App\ProductBatch;
use App\Sale;
use App\SaleProduct;
use Illuminate\Http\Request;

class CartController extends Controller {
    public function addCart(Request $request) {
        $user_token = $request->get('user_token');
        $product = $request->get('product');
        $count = $request->get('count');
        $type = $request->get('type') ?? 'web';
        $store_id = $request->get('store_id') ?? 1;
        if ($this->getCount($product, $store_id) < $count) {
            return ['error' => 'Недостаточно товара на складе'];
        }
        $cart_id = $this->createCart($user_token, $type, $store_id);
        $this->createCartProduct($product, $cart_id, $count);
        return new CartResource(Cart::find($cart_id));
    }

    public function increaseCount(Request $request) {
        $cart = $request->get('cart');
        $product = $request->get('product');
        $store_id = $request->get('store_id') ?? 1;
        $count = $request->get('count');
        if ($this->getCount($product, $store_id) <= $count) {
            return ['error' => 'Недостаточно товара на складе'];
        }

        $cartProduct = CartProduct::Cart($cart)->Product($product)->first();
        $cartProduct->update(['count' => $cartProduct['count'] + 1]);
        return new CartResource(Cart::find($cart));
    }

    public function deleteCart(Request $request) {

        $cart = $request->get('cart');

        $product = $request->get('product');

        CartProduct::Cart($cart)->Product($product)->delete();

        return new CartResource(Cart::find($cart));

    }

    public function decreaseCount(Request $request) {

        $cart = $request->get('cart');
        $product = $request->get('product');
        $store_id = $request->get('store_id') ?? 1;

        $cartProduct = CartProduct::Cart($cart)->Product($product)->first();

        if (intval($cartProduct['count']) === 1) {
            $cartProduct->delete();
        } else {
            $cartProduct->update(['count' => $cartProduct['count'] - 1]);
        }

        return new CartResource(Cart::find($cart));
    }

    public function getCart(Request $request) {

        $user_token = $request->get('user_token');
        $cart = Cart::where('user_token', $user_token)->first() ?? null;
        return new CartResource($cart);
    }

    public function order(Request $request) {
        $cart = $request->get('cart');
        $user_token = $request->get('user_token');
        $store_id = $request->get('store_id');
        $customer_info = $request->get('customer_info');
        $order = $this->createOrder($user_token, $store_id, $customer_info);
        $products = CartProduct::where('cart_id', $cart)->get();
        $this->createOrderProducts($order, $store_id, $products);
        CartProduct::where('cart_id', $cart)->delete();
        try {
            $this->sendTelegramMessage($order);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return $order->id;
    }

    public function sendTelegramMessage(Order $order) {
        $message = $this->getMessage($order);
        $telegram = new TelegramService();
        $telegram->sendMessage('-1001285942724', $message);
    }

    public function getMessage(Order $order) {
        $message = 'Новый заказ 💪💪💪' . "\n";
        $message .= 'Заказ №' . $order['id'] . "\n";
        $message .= 'ФИО: ' . $order['fullname'] . "\n";
        $message .= 'Номер телефона: ' . $order['phone'] . "\n";
        $message .= 'Адрес: ' . $order['address'] . "\n";

        $products = Product::with('attributes')->whereIn('id', $order->items->pluck('product_id'))->get();

        foreach ($products as $key => $product) {
            $attributes = $product->attributes->reduce(function ($a, $c) {
                return $c['attribute_value'] . ', ' . $a;
            }, '');
            $message .= ($key + 1) . '.' . $product->product_name . ',' . $attributes . '' . $product['product_price'] . 'тг' . "\n";
        }

        if ($order['comment']) {
            $message .= 'Комментарий:' . $order['comment'] . "\n";
        }

        $delivery = 'Доставка курьером';
        $payment = 'Оплата наличными';

        if ($order['delivery'] == 1) {
            $delivery = 'Самовывоз';
        }

        if ($order['payment'] == 1) {
            $payment = 'Оплата картой';
        }

        $message .= 'Способ оплаты: ' . $payment . "\n";
        $message .= 'Способ получения: ' . $delivery . "\n";

        $message .= 'Общая сумма: ' . $order->items->reduce(function ($a, $c) {
                return $a + $c['product_price'];
            }, 0) . 'тг' . "\n";

        $message .= "<a href='https://ironadmin.ariesdev.kz/api/order/" . $order['id'] . "/decline'>Отменить заказ❌</a>" . "\n";
        $message .= "<a href='https://ironadmin.ariesdev.kz/api/order/" . $order['id'] . "/accept'>Заказ выполнен✔️</a>";


        return urlencode($message);
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
        // @TODO после авторизации сделать клиент ид
        $sale = Sale::create([
            'client_id' => -1,
            'store_id' => $store_id,
            'user_id' => -1,
            'discount' => 0,
            'kaspi_red' => 0
        ]);
        foreach ($products as $product) {
            $product['sale_id'] = $sale['id'];
            unset($product['order_id']);
            unset($product['created_at']);
            unset($product['updated_at']);
            SaleProduct::create($product->toArray($product));
        }

        $order->status = 1;

        $order->update();

        return $order;
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

        return ['message' => 'ok'];

    }


    public function getTotal(Request $request) {
        $user_token = $request->get('user_token');
        $order = Order::where('user_token', $user_token)->first();
        if (!$order) {
            return null;
        }

        $products = $order->items;


        return intval(collect($products)->reduce(function ($i, $a) {
            return $i + $a['product_price'];
        }, 0));

    }

    /*
     * private methods
     * */

    private function createOrder($user_token, $store_id, $customer_info) {
        $order = ['user_token' => $user_token, 'store_id' => $store_id, 'payment' => $customer_info['paymentMethod'], 'delivery' => $customer_info['deliveryMethod'], 'fullname' => $customer_info['fullname'], 'address' => $customer_info['address'], 'phone' => $customer_info['phone'], 'city' => $customer_info['city'], 'email' => $customer_info['email'], 'comment' => $customer_info['comment'], 'status' => 0];
        return Order::create($order);
    }

    private function createOrderProducts($order, $store_id, $products) {
        foreach ($products as $product) {
            $count = intval($product['count']);
            for ($i = 0; $i < $count; $i++) {
                $product_batch = ProductBatch::where('product_id', $product['product_id'])->where('store_id', $store_id)->where('quantity', '>=', 1)->first();
                if ($product_batch) {
                    $product_sale = ['product_batch_id' => $product_batch['id'], 'product_id' => $product['product_id'], 'order_id' => $order['id'], 'purchase_price' => $product_batch['purchase_price'], 'product_price' => Product::find($product['product_id'])['product_price']];

                    OrderProduct::create($product_sale);

                    $quantity = $product_batch['quantity'] - 1;
                    $product_batch->update(['quantity' => $quantity]);
                }
            }
        }
    }

    private function createCart($user_token, $type, $store_id) {
        $cart = Cart::where('user_token', $user_token)->first();
        if (!$cart) {
            return Cart::create(['user_token' => $user_token, 'type' => $type, 'store_id' => $store_id])['id'];
        } else {
            return $cart['id'];
        }
    }

    private function getCount($product, $store_id) {
        $quantity = ProductBatch::where('product_id', $product)->where('store_id', $store_id)->sum('quantity');
        return $quantity;
    }

    private function createCartProduct($product, $cart_id, $count) {
        $cartProduct = CartProduct::Cart($cart_id)->Product($product)->first();
        if (!$cartProduct) {
            CartProduct::create(['cart_id' => $cart_id, 'product_id' => $product, 'count' => $count]);
        } else {
            $cartProduct->update(['count' => $cartProduct['count'] + $count]);
        }
    }

    private function getBatch($product, $store_id) {
        return ProductBatch::where('product_id', $product)->where('store_id', $store_id)->where('quantity', '>=', 1)->first();
    }
}
