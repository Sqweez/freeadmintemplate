<?php

namespace App\Repository\Opt;

use App\v2\Models\UserCart;
use App\v2\Models\WholesaleClient;

class CartRepository
{
    private ?WholesaleClient $client;
    private UserCart $cart;

    public function __construct(?WholesaleClient $client)
    {
        $this->client = $client;
        $this->cart = $this->retrieveCart();
    }

    public function addToCart(int $product_id, int $count): UserCart
    {
        return $this->cart;
    }

    private function retrieveCart(): UserCart
    {
        return $this->client->cart()->firstOrCreate([]);
    }
}
