<?php

namespace App\Http\Controllers\api;

use App\Cart;
use App\CartProduct;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\TelegramService;
use App\Http\Resources\shop\CartResource;
use App\Order;
use App\OrderProduct;
use App\v2\Models\ProductSku;
use App\ProductBatch;
use App\Sale;
use App\SaleProduct;
use App\Store;
use Illuminate\Http\Request;
use App\Client;
use App\ClientSale;
use App\ClientTransaction;
use Illuminate\Support\Facades\DB;


class CartController extends Controller {
    protected $PAYMENT_CONFIRMED = 1;
    protected $PAYMENT_REJECTED = 0;

    public function addCart(Request $request) {
        $user_token = $request->get('user_token');
        $product = $request->get('product');
        $count = $request->get('count');
        $type = $request->get('type') ?? 'web';
        $store_id = $request->get('store_id') ?? 1;
        $cart = Cart
            ::where('user_token', $user_token)
            ->firstOrCreate([
                    'user_token' => $user_token,
                    'type' => $type,
                    'store_id' => $store_id
                ]
            );
        $products = $cart->products()->where('product_id', $product)->first();
        if ($products) {
            $products->count += $count;
            $products->save();
        } else {
            $cart->products()->create([
                'product_id' => $product,
                'count' => $count
            ]);
        }



        return new CartResource(
            Cart::whereKey($cart->id)
                ->with([
                    'products', 'products.product',
                    'products.product.attributes', 'products.product.product.attributes'])
                ->with(['products.product.batches' => function ($q) use ($store_id) {
                    return $q->where('store_id', $store_id)->where('quantity', '>', 0);
                }])
                ->first()
        );
    }

    public function increaseCount(Request $request) {
        $cart = $request->get('cart');
        $product = $request->get('product');
        $store_id = $request->get('store_id') ?? 1;
        $count = $request->get('count');
        if ($this->getCount($product, $store_id) <= $count) {
            return response()->json(['error' => 'Недостаточно товара на складе'], 419);
        }

        $cartProduct = CartProduct::Cart($cart)->Product($product)->first();
        $cartProduct->increment('count');
        $cartProduct->save();

        $cartProduct->fresh();

        return response()->json([
            'id' => $cartProduct->product_id,
            'count' => $cartProduct->count
        ], 200);
    }

    public function deleteCart(Request $request) {

        $cart = $request->get('cart');

        $product = $request->get('product');

        CartProduct::where('cart_id', $cart)->where('product_id', $product)->delete();

        return response()->json([]);

    }

    public function decreaseCount(Request $request) {

        $cart = $request->get('cart');
        $product = $request->get('product');
        $store_id = $request->get('store_id') ?? 1;

        $cartProduct = CartProduct::Cart($cart)->Product($product)->first();

        if (intval($cartProduct['count']) === 1) {
            $cartProduct->delete();
        } else {
            $cartProduct->decrement('count');
            $cartProduct->save();
        }

        $cartProduct->fresh();

        return response()->json([
            'id' => $cartProduct->product_id,
            'count' => $cartProduct->count
        ], 200);

    }

    public function getCart(Request $request) {
        $user_token = $request->get('user_token');
        $store_id = $request->get('store_id');
        $cart = Cart::with([
                'products', 'products.product',
                'products.product.attributes', 'products.product.product.attributes'])
                ->ofUser($user_token)
                ->with(['products.product.batches' => function ($q) use ($store_id) {
                    return $q->where('store_id', $store_id)->where('quantity', '>', 0);
                }])
                ->first() ?? null;
        if ($cart && $store_id != $cart['store_id']) {
            CartProduct::where('cart_id', $cart['id'])->delete();
            $cart->delete();
            $cart = null;
        }

        if ($cart === null) {
            return null;
        }

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


        try {
            DB::beginTransaction();
            $order = $this->createOrder($user_token, $store_id, $customer_info, $client_id, $discount);
            $products = CartProduct::where('cart_id', $cart)->get();
            $this->createOrderProducts($order, $store_id, $products);
            CartProduct::where('cart_id', $cart)->delete();

            if ($customer_info['is_paid']) {
                try {
                    $this->sendTelegramMessage($order);
                } catch (\Exception $e) {
                    dd($e->getMessage());
                }
            }
            DB::commit();

            return response()->json([
                'order' => intval($order->id)
            ], 200);
        } catch (\Exception $exception) {
            DB::rollBack();;
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }

    }

    private function sendTelegramMessage(Order $order, $result = null) {
        $message = $this->getMessage($order, $result);
        $telegram = new TelegramService();
        $store = Store::where('id', $order['city'])->first();
        $telegram->sendMessage($store->telegram_chat_id, $message);
    }

    public function telegramMessage(Order $order, Request $request) {
        $result = intval($request->get('result'));
        return $this->sendTelegramMessage($order, $result);
    }

    public function getMessage(Order $order, $result = null) {
        $store = Store::where('id', $order['city'])->first();
        $message = 'Новый заказ 💪💪💪' . "\n";
        $message .= 'Заказ №' . $order['id'] . "\n";
        $message .= 'ФИО: ' . $order['fullname'] . "\n";
        $message .= 'Номер телефона: ' . $order['phone'] . "\n";
        $message .= 'Город: ' . $store->city . "\n";
        $message .= 'Адрес: ' . $order['address'] . "\n";

        $discount = $order['discount'];

        $products = ProductSku::with(['product', 'product.attributes', 'attributes'])->whereIn('id', $order->items->pluck('product_id'))->get();
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

        if ($order['payment'] == 2) {
            if (intval($result) === $this->PAYMENT_CONFIRMED) {
                $payment = 'Онлайн оплата: ОПЛАЧЕНО!';
            } else {
                $payment = 'Онлайн оплата: ОПЛАТА НЕ ПРОШЛА!';
            }
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

        $message = 'Заказ №' . $order->id . ' выполнен 💪💪💪';

        (new TelegramService())->sendMessage($order->store->telegram_chat_id, urlencode($message));

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

        (new TelegramService())->sendMessage($order->store->telegram_chat_id, urlencode($message));

        return 'Заказ отменен!';

    }


    public function getTotal(Request $request) {
        $user_token = $request->get('user_token');
        $order = Cart::with(['products', 'products.product.product:id,product_price'])->select(['id'])->where('user_token', $user_token)->first();
        if (!$order) {
            return null;
        }

        $total =  $order->products->reduce(function ($a, $c) {
            return $a + $c['count'] * $c['product']['product_price'];
        }, 0);

        return response()->json([
            'total' => $total
        ], 200);
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
        try {
            foreach ($products as $product) {
                $count = intval($product['count']);
                for ($i = 0; $i < $count; $i++) {
                    $product_batch = ProductBatch::where('product_id', $product['product_id'])->where('store_id', $store_id)->where('quantity', '>=', 1)->first();
                    if ($product_batch) {
                        $product_sale = [
                            'product_batch_id' => $product_batch['id'],
                            'product_id' => $product['product_id'],
                            'order_id' => $order['id'],
                            'purchase_price' => $product_batch['purchase_price'],
                            'product_price' => ProductSku::find($product['product_id'])['product_price']
                        ];

                        OrderProduct::create($product_sale);

                        $quantity = $product_batch['quantity'] - 1;
                        $product_batch->update(['quantity' => $quantity]);
                    }
                }
            }
        } catch (\Exception $exception) {
            throw new $exception;
        }
    }

    private function getCount($product, $store_id) {
        return intval(ProductBatch::where('product_id', $product)->where('store_id', $store_id)->sum('quantity'));
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
