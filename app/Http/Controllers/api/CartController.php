<?php

namespace App\Http\Controllers\api;

use App\Cart;
use App\CartProduct;
use App\Client;
use App\ClientSale;
use App\ClientTransaction;
use App\Http\Controllers\Controller;
use App\Http\Resources\shop\CartResource;
use App\Jobs\Notifications\Order\SendOrderNotificationJob;
use App\Order;
use App\OrderProduct;
use App\ProductBatch;
use App\Sale;
use App\Store;
use App\User;
use App\v2\Models\ProductSku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use TelegramService;

class CartController extends Controller {
    protected $PAYMENT_CONFIRMED = 1;
    protected $PAYMENT_REJECTED = 0;

    public function addCart(Request $request) {
        $user_token = $request->get('user_token');
        $product = $request->get('product');
        $count = $request->get('count');
        $type = $request->get('type') ?? 'web';
        $store_id = $request->get('store_id') ?? 1;
        $discount = $request->get('discount', 0);
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
                'count' => $count,
                'discount' => $discount
            ]);
        }



        return new CartResource(
            Cart::whereKey($cart->id)
                ->with([
                    'products', 'products.product',
                    'products.product.product.stocks',
                    'products.product.product.manufacturer',
                    'products.product.product.subcategory',
                    'products.product.attributes',
                    'products.product.product.attributes'
                ])
                ->with(['products.product.batches' => function ($q) use ($store_id) {
                    if (intval($store_id) === -1) {
                        return $q->whereIn('store_id', [1, 6])->where('quantity', '>', 0);
                    }
                    return $q->where('store_id', $store_id)->where('quantity', '>', 0);
                }])
                ->first()
        );
    }

    public function increaseCount(Request $request) {
        $cart = $request->get('cart');
        $product = $request->get('product');
        $store_id = intval($request->get('store_id')) ?? 1;
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

    public function changeCart(Request $request): ?CartResource
    {
        $cartItem = CartProduct::find($request->get('cart_item_id'));
        if ($cartItem) {
            $cartItem->update([
                'count' => 1,
                'product_id' => $request->get('product_id')
            ]);
        }

        return $this->getCart($request);
    }

    public function deleteCart(Request $request): ?CartResource
    {

        $cart = $request->get('cart');
        $userToken = $request->get('user_token');

        $product = $request->get('product');

        $cartProduct = CartProduct::query()
            ->where(function ($query) use ($cart, $userToken) {
                return $query
                    ->where('cart_id', $cart);
            })
            ->where('product_id', $product)
            ->first();

        if ($cartProduct) {
            if ($cartProduct->kit_slug) {
                CartProduct::query()
                    ->where('cart_id', $cartProduct->cart_id)
                    ->whereKeyNot($cartProduct->id)
                    ->where('kit_slug', $cartProduct->kit_slug)
                    ->delete();
            }

            $cartProduct->delete();
        }

        return $this->getCart($request);

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

    private function retrieveSpecialItemIdById($id, $store_id)
    {
        $gainerSkus = ProductSku::find($id)->relativeSku;
        $gainerSkus->load(['batches' => function ($q) use ($store_id) {
            return $q->where('store_id', $store_id)->where('quantity', '>', 0);
        }]);
        $skuId = $gainerSkus->map(function ($sku) {
            return [
                'id' => $sku->id,
                'quantity' => $sku->batches->sum('quantity')
            ];
        })->sortByDesc('quantity')->first();
        return $skuId ? $skuId['id'] : null;
    }


    public function getCart(Request $request) {
        $user_token = $request->get('user_token');
        $store_id = $request->get('store_id');
        $cart = Cart
            ::where('user_token', $user_token)
            ->firstOrCreate([
                    'user_token' => $user_token,
                    'type' => 'web',
                    'store_id' => $store_id
                ]
            );

        if ($request->has('stock')) {
            $stock = $request->get('stock');
            if ($stock === 'moshch-i-obem-v-odnom-nabore') {
                CartProduct::where([
                    'cart_id' => $cart->id,
                    'kit_slug' => $stock
                ])->delete();
                $gainerSkuId = $this->retrieveSpecialItemIdById(9400, $store_id);
                $creatineId = $this->retrieveSpecialItemIdById(9387, $store_id);
                $giftId = $this->retrieveSpecialItemIdById(6617, $store_id);
                $cart = Cart
                    ::where('user_token', $user_token)
                    ->firstOrCreate([
                            'user_token' => $user_token,
                            'type' => 'web',
                            'store_id' => $store_id
                        ]
                    );

                if ($gainerSkuId && $creatineId && $giftId) {

                    $cart->products()->firstOrCreate([
                        'product_id' => $gainerSkuId,
                        'count' => 1,
                        'discount' => 0,
                        'kit_slug' => $stock,
                        'has_other_discounts' => false,
                    ]);
                    $cart->products()->firstOrCreate([
                        'product_id' => $creatineId,
                        'count' => 1,
                        'discount' => 0,
                        'kit_slug' => $stock,
                        'has_other_discounts' => false,
                    ]);
                    $cart->products()->firstOrCreate([
                        'product_id' => $giftId,
                        'count' => 1,
                        'discount' => 100,
                        'kit_slug' => $stock,
                        'has_other_discounts' => false,
                    ]);
                }
            }
            if ($stock === 'udvoy-svoy-rezultat') {
                $gainerSkuId = $this->retrieveSpecialItemIdById(9396, $store_id);
                $giftId = $this->retrieveSpecialItemIdById(9387, $store_id);
                CartProduct::where([
                    'cart_id' => $cart->id,
                    'kit_slug' => $stock
                ])->delete();

                if ($gainerSkuId && $giftId) {

                    $cart->products()->firstOrCreate([
                        'product_id' => $gainerSkuId,
                        'count' => 1,
                        'discount' => 0,
                        'kit_slug' => $stock,
                        'has_other_discounts' => false,
                    ]);
                    $cart->products()->firstOrCreate([
                        'product_id' => $giftId,
                        'count' => 1,
                        'discount' => 100,
                        'kit_slug' => $stock,
                        'has_other_discounts' => false,
                    ]);
                }
            }

            if ($stock === 'mnogo-belka-dolgaya-sytost-i-udobstvo-v-odnom-nabore-whey') {
                $product1 = $this->retrieveSpecialItemIdById(9014, $store_id);
                $giftId = $this->retrieveSpecialItemIdById(6617, $store_id);
                CartProduct::where([
                    'cart_id' => $cart->id,
                    'kit_slug' => $stock
                ])->delete();
                if ($product1 && $giftId) {
                    $cart->products()->firstOrCreate([
                        'product_id' => $product1,
                        'count' => 1,
                        'discount' => 0,
                        'kit_slug' => $stock
                    ]);
                    $cart->products()->firstOrCreate([
                        'product_id' => $giftId,
                        'count' => 1,
                        'discount' => 100,
                        'kit_slug' => $stock
                    ]);
                }
            }
            if ($stock === 'mnogo-belka-dolgaya-sytost-i-udobstvo-v-odnom-nabore-casein') {
                $product2 = $this->retrieveSpecialItemIdById(9026, $store_id);
                $giftId = $this->retrieveSpecialItemIdById(6617, $store_id);
                CartProduct::where([
                    'cart_id' => $cart->id,
                    'kit_slug' => $stock
                ])->delete();
                if ($product2 && $giftId) {
                    $cart->products()->firstOrCreate([
                        'product_id' => $product2,
                        'count' => 1,
                        'discount' => 0,
                        'kit_slug' => $stock
                    ]);
                    $cart->products()->firstOrCreate([
                        'product_id' => $giftId,
                        'count' => 1,
                        'discount' => 100,
                        'kit_slug' => $stock
                    ]);
                }
            }

        }
        $cart = Cart::with([
            'products', 'products.product',
            'products.product.product.stocks',
            'products.product.attributes', 'products.product.product.attributes'])
            ->ofUser($user_token)
            ->with(['products.product.batches' => function ($q) use ($store_id) {
                return $q/*->where('store_id', $store_id)*/->where('quantity', '>', 0);
            }])
            ->with(['products.product.relativeSku.batches' => function ($q) use ($store_id) {
                return $q->where('quantity', '>', 0)->where('store_id', $store_id);
            }])
            ->with('products.product.relativeSku.attributes')
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
        \Log::info('order', $request->all());
        $cart = $request->get('cart');
        $user_token = $request->get('user_token');
        $store_id = $request->get('store_id');
        $customer_info = $request->get('customer_info');
        # $other_discount = $request->has('discount') ? intval($request->get('discount')) : 0;
        $is_iherb = $request->has('iherb');
        $promocode = $request->get('promocode', null);
        $products = $request->get('products', []);
        $gifts = $request->get('gifts', []);

        // TODO @2023-05-03T00:52:06 UGLY REWORK!!!
        foreach ($products as $item) {
            CartProduct::query()
                ->where('id', $item['cart_product_id'])
                ->update(['discount' => $item['discount']]);
        }

        foreach ($gifts as $gift) {
            CartProduct::query()
                ->create([
                    'cart_id' => $cart,
                    'product_id' => $gift,
                    'discount' => 100,
                    'count' => 1,
                ]);
        }

        $client_id = -1;
        $discount = 0;

        if (isset($customer_info['client_id'])) {
            $client_id = $customer_info['client_id'];
            $__client = Client::find($client_id);
            if ($__client) {
                $discount = $__client->client_discount;
            }
        };

        $phone = unmask_phone($customer_info['phone']);

        if ($client_id === -1) {
            $client = Client::whereClientPhone($phone)->first();
            if (!$client) {
                $client = Client::query()
                    ->create([
                        'client_phone' => $phone,
                        'client_name' => $customer_info['fullname'],
                        'client_city' => $customer_info['city'],
                        'client_card' => '',
                        'loyalty_id' => 1,
                        'gender' => 'M'
                    ])
                    ->refresh();
            }
            $client_id = $client->id;
        }

        try {
            DB::beginTransaction();
            $order = $this->createOrder($user_token, $store_id, $customer_info, $client_id, $discount, $is_iherb, $promocode);
            $products = CartProduct::where('cart_id', $cart)->get();
            $this->createOrderProducts($order, $store_id, $products);
            CartProduct::where('cart_id', $cart)->delete();
            SendOrderNotificationJob::dispatch($order);
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

    public function updateOrder(Order $order, Request $request) {
        $order->update([
            'is_paid' => !!$request->get('result')
        ]);
    }

    public function getOrderAmount(Order $order) {
        $discount = $order['discount'];
        return ceil($order->items->reduce(function ($a, $c) use ($discount){
                return $a + ($c['product_price'] * ((100 - intval($discount)) / 100));
            }, 0) - intval($order['balance']));
    }

    private function sendTelegramMessage(Order $order, $result = null) {
        $message = $this->getMessage($order, $result);
        $store = Store::where('id', $order['city'])->first();
        TelegramService::sendMessage($store->telegram_chat_id, $message);
    }

    public function telegramMessage(Order $order, Request $request) {
        $result = intval($request->get('result'));
        $order->update([
            'is_paid' => !!$result
        ]);
        $status = $result === 1 ? 'Оплачен!' : 'Оплата не прошла!';
        $message = 'Заказ №' . $order->id . ' СТАТУС ИЗМЕНЕН' . "\n";
        $message .= 'Статус заказа: ' . $status;
        TelegramService::sendMessage($order->store->telegram_chat_id, urlencode($message));
    }

    public function getMessage(Order $order, $result = null): string {
        $store = Store::where('id', $order['city'])->first();
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
        $waString = sprintf(
            'https://api.whatsapp.com/send?phone=%s&text=%s',
            \Str::replaceFirst('+', '', $order['phone']),
            'Здравствуйте, Ваш заказ принят и передан курьеру. Ожидайте доставку c ?? до ??. (с) Служба заботы о клиентах “Iron addicts”'
        );
        $message .= "\n" . "<a href='" . $waString . "'>Отправить в WA клиенту</a>";

        return urlencode($message);
    }

    private function getDeliveryCost($city, $total, $deliveryMethod): int {
        if ($deliveryMethod === 1) {
            return 0;
        }
        return $total - $city['delivery_threshold'] >= 0 ? 0 : $city['delivery_cost'];
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

    public function getCartCount(Request $request) {
        $user_token = $request->get('user_token');
        $order = Cart::with(['products'])->select(['id'])->where('user_token', $user_token)->first();
        if (!$order) {
            return 0;
        }

        $total =  $order->products->reduce(function ($a, $c) {
            return $a + $c['count'];
        }, 0);

        return response()->json([
            $total
        ], 200);
    }

    /*
     * private methods
     * */

    private function createOrder($user_token, $store_id, $customer_info, $client_id, $discount, $is_iherb, $promocode) {
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
            'balance' => $customer_info['balance'],
            'is_paid' => 0,
            'is_iherb' => $is_iherb
        ];

        if ($promocode) {
            $order['promocode_id'] = $promocode['id'];
            if ($promocode['promocode_type_id'] === __hardcoded(2)) {
                $order['promocode_fixed_amount'] = $promocode['discount'];
            }
        }

        return Order::create($order)->refresh();
    }

    private function createOrderProducts($order, $store_id, $products) {
        try {
            foreach ($products as $product) {
                $count = intval($product['count']);
                for ($i = 0; $i < $count; $i++) {
                    $_store_id = intval($store_id);
                    if ($_store_id === -1) {
                        $product_batch = ProductBatch::where('product_id', $product['product_id'])->whereIn('store_id', [1, 6])->where('quantity', '>=', 1)->first();
                    } else {
                        $product_batch = ProductBatch::where('product_id', $product['product_id'])->where('store_id', $store_id)->where('quantity', '>=', 1)->first();
                    }
                    if ($product_batch) {
                        $sku = ProductSku::find($product['product_id']);
                        $mainProduct = $sku->product;
                        $productPrice = $mainProduct->stock_price;
                        $product_sale = [
                            'product_batch_id' => $product_batch['id'],
                            'product_id' => $product['product_id'],
                            'order_id' => $order['id'],
                            'purchase_price' => $product_batch['purchase_price'],
                            'product_price' => $productPrice,
                            'discount' => max($product['discount'], $order['discount'])
                        ];

                        if (!$product['has_other_discounts']) {
                            $product_sale['discount'] = $product['discount'];
                        }

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
        if ($store_id === -1) {
            return intval(ProductBatch::where('product_id', $product)->whereIn('store_id', [1, 6])->sum('quantity'));
        }
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


    public function createClientSale(Sale $sale) {

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
            'user_id' => User::IRON_WEB_STORE
        ]);

        if ($sale['balance'] > 0) {
            ClientTransaction::create([
                'client_id' => $client_id,
                'sale_id' => $sale['id'],
                'amount' => $sale['balance'] * -1,
                'user_id' => User::IRON_WEB_STORE
            ]);
        }
    }

}
