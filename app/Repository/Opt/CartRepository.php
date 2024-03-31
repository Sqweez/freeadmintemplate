<?php

namespace App\Repository\Opt;

use App\Repository\ProductBatchRepository;
use App\Store;
use App\v2\Models\UserCart;
use App\v2\Models\WholesaleClient;

class CartRepository
{
    private ?WholesaleClient $client;
    private UserCart $cart;
    private Store $store;

    /**
     * @throws \Exception
     */
    public function __construct(?WholesaleClient $client)
    {
        $this->client = $client;
        if (!$this->client) {
            throw new \Exception('При добавлении товара в корзину произошла ошибка');
        }
        $this->cart = $this->retrieveCart();
        $this->store = $this->retrieveStore();
    }

    /**
     * @throws \Exception
     */
    public function addToCart(int $product_id, int $count)
    {
        $availableQuantity = app(ProductBatchRepository::class)->getProductQuantityInStore($product_id, $this->store);
        $inCartCount = $this->getInCartProductCount($product_id);
        $quantityDelta = $availableQuantity - $count - $inCartCount;
        if ($quantityDelta < 0) {
            throw new \Exception('Недостаточно товара');
        }
        return [
            'inCart' => $inCartCount,
            'available'=> $availableQuantity,
            'needle' => $count
        ];
    }

    private function getInCartProductCount($productId)
    {
        return $this->cart->items()->where('product_id', $productId)->sum('count');
    }

    private function retrieveCart(): UserCart
    {
        return $this->client->cart()->firstOrCreate([]);
    }

    private function retrieveStore()
    {
        return Store::whereTypeId(4)
            ->first();
    }
}
