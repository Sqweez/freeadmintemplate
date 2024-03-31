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

    public function create(WholesaleClient $client, array $payload)
    {
        \DB::transaction(function () use ($client, $payload) {
            $order = $this->createOrder($client, $payload);
            $products = $this->createOrderProducts($order, $client);
        });
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

    private function createOrderProducts(WholesaleOrder $order, WholesaleClient $client)
    {
        $cartProducts = $client->cart->items;
        foreach ($cartProducts as $cartProduct) {
            $existingQuantities = $this->productBatchRepository->getWholesaleProductsQuantity($cartProduct['product_id']);
        }
    }
}
