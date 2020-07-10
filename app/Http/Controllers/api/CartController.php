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
use App\Client;
use App\ClientSale;
use App\ClientTransaction;


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
        $cart = Cart::ofUser($user_token)->first() ?? null;
        return new CartResource($cart);
    }

    public function order(Request $request) {
        $cart = $request->get('cart');
        $user_token = $request->get('user_token');
        $store_id = $request->get('store_id');
        $customer_info = $request->get('customer_info');

        $client_id = -1;
        $discount = 0;

        if (isset($customer_info['client_id'])) {
            $client_id = $customer_info['client_id'];
            $discount = Client::find($client_id)['client_discount'];
        };

        $order = $this->createOrder($user_token, $store_id, $customer_info, $client_id, $discount);
        $products = CartProduct::where('cart_id', $cart)->get();
        $this->createOrderProducts($order, $store_id, $products);
        CartProduct::where('cart_id', $cart)->delete();
        try {
            $this->sendTelegramMessage($order);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return intval($order->id);
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
        $message .= 'Город: ' . $order['city'] . "\n";
        $message .= 'Адрес: ' . $order['address'] . "\n";

        $discount = $order['discount'];

        $products = Product::with('attributes')->whereIn('id', $order->items->pluck('product_id'))->get();
        $cartProducts = collect($order->items);

        foreach ($products as $key => $product) {
            $attributes = $product->attributes->reduce(function ($a, $c) {
                return $c['attribute_value'] . ', ' . $a;
            }, '');

            $count = $cartProducts->filter(function ($i) use ($product) {
                return $i['product_id'] == $product['id'];
            })->count();
            $message .= ($key + 1) . '.' . $product->product_name . ',' . $attributes . ' ' . $product['product_price'] . 'тг' . ' | ' . $count . 'шт.' . "\n";
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

        $message .= 'Общая сумма: ' . ceil($order->items->reduce(function ($a, $c) use ($discount){
                    return $a + ($c['product_price'] * ((100 - intval($discount)) / 100));
                }, 0) - intval($order['balance'])) . 'тг' . "\n";

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

        $sale = Sale::create([
            'client_id' => $order['client_id'],
            'store_id' => $store_id,
            'user_id' => 2,
            'discount' => $order['discount'],
            'kaspi_red' => 0,
            'balance' => $order['balance'] ?? 0
        ]);


        foreach ($products as $product) {
            $product['sale_id'] = $sale['id'];
            unset($product['order_id']);
            unset($product['created_at']);
            unset($product['updated_at']);
            unset($product['id']);
            SaleProduct::create($product->toArray($product));
        }


        $this->createClientSale($sale);

        $order->status = 1;

        $order->update();

        if (intval($order['balance']) > 0) {

        }

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

        return 'Заказ отменен!';

    }


    public function getTotal(Request $request) {
        $user_token = $request->get('user_token');
        $order = Cart::where('user_token', $user_token)->first();
        if (!$order) {
            return null;
        }

        $products = $order->products;

        return intval(collect($products)->reduce(function ($i, $a) {
            $product_price = Product::find($a['product_id'])['product_price'];
            return $i + ($a['count'] * $product_price);
        }, 0));
    }

    /*
     * private methods
     * */

    private function createOrder($user_token, $store_id, $customer_info, $client_id, $discount) {
        $order = [
            'user_token' => $user_token,
            'store_id' => $store_id,
            'payment' => $customer_info['paymentMethod'],
            'delivery' => $customer_info['deliveryMethod'],
            'fullname' => $customer_info['fullname'],
            'address' => $customer_info['address'],
            'phone' => $customer_info['phone'],
            'city' => $customer_info['city'],
            'email' => $customer_info['email'],
            'comment' => $customer_info['comment'],
            'status' => 0,
            'client_id' => $client_id,
            'discount' => $discount,
            'balance' => $customer_info['balance']
        ];
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

    public static function mergeCarts($request_token, $user_token) {
        $guestCart = Cart::ofUser($request_token)->get()->first();
        if (!$guestCart) {
            return;
        }
        $cart = Cart::ofUser($user_token)->first();
        if ($guestCart && !$cart) {
            $guestCart->update(['user_token' => $user_token]);
            return;
        }
        if ($guestCart && $cart) {
            CartProduct::Cart($guestCart->id)->update(['cart_id' => $cart->id]);
            $guestCart->delete();
            $groupedProducts = collect($cart->products)->groupBy('product_id');
            $groupedProducts->each(function ($i) {
                $count = $i->sum('count');
                $i[0]->count = $count;
                $i[0]->save();
                for ($x = 1; $x < count($i); $x++) {
                    $i[$x]->delete();
                }
            });

        }
    }

    public static function groupCart() {

    }

    private function createClientSale(Sale $sale) {

        $client_id = $sale['client_id'];

        if ($client_id === -1) {
            return null;
        }

        $discount = intval($sale['discount']);
        $products = $sale->products;
        $amount = collect($products)->reduce(function ($c, $i) use ($discount) {
            return $c + ($i['product_price'] * ((100 - $discount) / 100));
        });


        ClientSale::create([
            'client_id' => $client_id,
            'amount' => $amount,
            'sale_id' => $sale['id']
        ]);


        ClientTransaction::create([
            'client_id' => $client_id,
            'sale_id' => $sale['id'],
            'amount' => $amount * 0.01,
            'user_id' => 2
        ]);

        if ($sale['balance'] > 0) {
            ClientTransaction::create([
                'client_id' => $client_id,
                'sale_id' => $sale['id'],
                'amount' => $sale['balance'] * -1,
                'user_id' => 2
            ]);
        }


    }


}
