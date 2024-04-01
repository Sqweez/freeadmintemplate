<?php

namespace App\Repository\Opt;

use App\Repository\ProductBatchRepository;
use App\v2\Models\WholesaleClient;
use App\v2\Models\WholesaleOrder;

class OrderRepository
{

    private ProductBatchRepository $productBatchRepository;

    public function __construct()
    {
        $this->productBatchRepository = new ProductBatchRepository();
    }

    /**
     * @throws \Throwable
     */
    public function create(WholesaleClient $client, array $payload)
    {
        return \DB::transaction(function () use ($client, $payload) {
            $order = $this->createOrder($client, $payload);
            $this->createOrderProducts($order, $client);
            $order->setStatusCreate();
            $this->clearCart($client);
            return true;
        });
    }

    public function getOrdersHistory(WholesaleClient $client)
    {
        return WholesaleOrder::query()
            ->byClient($client->id)
            ->with('products:id,product_id,price,wholesale_order_id')
            ->with('currentStatus')
            ->get();
    }

    private function createOrder(WholesaleClient $client, array $payload): WholesaleOrder
    {
        $wholesaleOrder = new WholesaleOrder();
        $wholesaleOrder->wholesale_client_id = $client->id;
        $wholesaleOrder->email = $payload['email'];
        $wholesaleOrder->phone = $payload['phone'];
        $wholesaleOrder->payment_type_id = $payload['payment_type_id'];
        $wholesaleOrder->delivery_type_id = $payload['delivery_type_id'];
        $wholesaleOrder->name = $payload['name'];
        $wholesaleOrder->comment = $payload['comment'];
        $wholesaleOrder->save();
        return $wholesaleOrder;
    }

    /**
     * @throws \Exception
     */
    private function createOrderProducts(WholesaleOrder $order, WholesaleClient $client)
    {
        $cartProducts = $client->cart->items;
        $cartProducts->load(['product.product.wholesale_prices' => function ($query) use ($client) {
            return $query->where('currency_id', $client->preferred_currency_id);
        }]);
        foreach ($cartProducts as $cartProduct) {
            $existingQuantities = $this->productBatchRepository->getWholesaleProductsQuantity($cartProduct['product_id']);
            if ($existingQuantities < $cartProduct['count']) {
                throw new \Exception('Некоторых товаров недостаточно на складе');
            }
            for ($i = 0; $i < $cartProduct['count']; $i++) {
                $batch = $this->productBatchRepository->changeWholesaleProductQuantity($cartProduct['product_id'], -1);
                $order->products()
                    ->create([
                        'product_id' => $cartProduct['product_id'],
                        'currency_id' => $client->preferred_currency_id,
                        'purchase_price' => $batch->purchase_price,
                        'price' => $cartProduct->getPrice(),
                    ]);
            }
        }
    }

    private function clearCart(WholesaleClient $client)
    {
        return $client->cart->items()->delete();
    }
}
