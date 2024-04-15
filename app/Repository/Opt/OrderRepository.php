<?php

namespace App\Repository\Opt;

use App\Events\Opt\WholesaleOrderCreated;
use App\Repository\ProductBatchRepository;
use App\v2\Models\WholesaleClient;
use App\v2\Models\WholesaleOrder;
use App\v2\Models\WholesaleOrderProduct;
use Exception;

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
            WholesaleOrderCreated::dispatch($order);
            return true;
        });
    }

    public function getOrdersHistory(WholesaleClient $client)
    {
        return WholesaleOrder::query()
            ->byClient($client->id)
            ->with('products:id,product_id,price,wholesale_order_id')
            ->with('status.status')
            ->orderByDesc('created_at')
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
        $wholesaleOrder->currency_id = $client->preferred_currency_id;
        $wholesaleOrder->discount = $client->cart->discount;
        $wholesaleOrder->save();
        return $wholesaleOrder;
    }

    /**
     * @throws Exception
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
                throw new Exception('Некоторых товаров недостаточно на складе');
            }
            for ($i = 0; $i < $cartProduct['count']; $i++) {
                $batch = $this->productBatchRepository->changeWholesaleProductQuantity($cartProduct['product_id'], -1);
                $order->products()
                    ->create([
                        'product_id' => $cartProduct['product_id'],
                        'currency_id' => $client->preferred_currency_id,
                        'purchase_price' => $batch->purchase_price,
                        'price' => $cartProduct->getPrice(),
                        'product_batch_id' => $batch->id,
                        'discount' => $cartProduct['discount'],
                    ]);
            }
        }
    }

    private function clearCart(WholesaleClient $client)
    {
        return $client->cart->items()->delete();
    }

    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function updateOrder(WholesaleOrder $order, array $products, array $deleted = [])
    {
        \DB::transaction(function () use ($order, $products, $deleted) {
            $this->deleteItemsFromOrder($deleted);
            $this->processOrderProducts($order, $products);
        });
    }

    /**
     * @throws Exception
     */
    private function deleteItemsFromOrder(array $deleted)
    {
        if (count($deleted) === 0) {
            return ;
        }
        WholesaleOrderProduct::query()
            ->whereIn('id', $deleted)
            ->get()
            ->groupBy('product_batch_id')
            ->each(function ($batches, $key) {
                $this->productBatchRepository->increaseBatchQuantity($key, $batches->count());
            });

        WholesaleOrderProduct::query()
            ->whereIn('id', $deleted)
            ->delete();
    }

    /**
     * @throws Exception
     */
    private function processOrderProducts(WholesaleOrder $order, array $products)
    {
        foreach ($products as $product) {
            if ($product['deltaCount'] < 0) {
                $ids = collect($product['ids'])->take($product['deltaCount'] * -1)->toArray();
                $this->deleteItemsFromOrder($ids);
            }
            else if ($product['deltaCount'] > 0) {
                $this->addProductToOrder($order, $product);
            } else {
                $this->updateOrderProduct($order, $product);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function addProductToOrder(WholesaleOrder $order, array $product)
    {
        $existingQuantities = $this->productBatchRepository->getWholesaleProductsQuantity($product['product_id']);
        if ($existingQuantities < $product['deltaCount']) {
            throw new Exception('На складе недостаточно некоторых товаров');
        }

        for ($i = 0; $i < $product['deltaCount']; $i++) {
            $batch = $this->productBatchRepository->changeWholesaleProductQuantity($product['product_id'], -1);
            $order->products()
                ->create([
                    'product_id' => $product['product_id'],
                    'currency_id' => $order->currency_id,
                    'purchase_price' => $batch->purchase_price,
                    'price' => $product['price'],
                    'product_batch_id' => $batch->id,
                    'discount' => $product['discount']
                ]);
        }
    }

    private function updateOrderProduct(WholesaleOrder $order, $product)
    {
        WholesaleOrderProduct::whereIn('id', $product['ids'])
            ->update([
                'price' => $product['price'],
                'discount' => $product['discount'],
            ]);
    }
}
