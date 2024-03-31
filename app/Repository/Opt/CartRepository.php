<?php

namespace App\Repository\Opt;

use App\Repository\ProductBatchRepository;
use App\Store;
use App\v2\Models\UserCart;
use App\v2\Models\WholesaleClient;
use App\v2\Models\WholesalePrice;
use Exception;

class CartRepository
{
    private ?WholesaleClient $client;
    private UserCart $cart;
    private Store $store;
    private ProductBatchRepository $productBatchRepository;

    /**
     * @throws Exception
     */
    public function __construct(?WholesaleClient $client)
    {
        $this->client = $client;
        if (!$this->client) {
            throw new Exception('При добавлении товара в корзину произошла ошибка');
        }
        $this->cart = $this->retrieveCart();
        $this->store = $this->retrieveStore();
        $this->productBatchRepository = new ProductBatchRepository();
    }

    public function getCart(): \Illuminate\Database\Eloquent\Collection
    {
        $cartItems = $this->cart->items()->with(['product.product.wholesale_prices' => function ($q) {
            return $q->where('currency_id', $this->client->preferred_currency_id);
        }])->get();
        return $cartItems;
    }

    public function getTotal(): array
    {
        $cartItems = $this->cart->items()->with('product')->get();
        $prices = WholesalePrice::query()
            ->whereIn('product_id', $cartItems->pluck('product.product_id')->toArray())
            ->where('currency_id', $this->client->preferred_currency_id)
            ->get();
        $subTotal = 0;
        $discountTotal = 0;
        $total = 0;
        foreach ($cartItems as $cartItem) {
            $needlePrice = $prices->where('product_id', $cartItem['product']['product_id'])->first()->price;
            $price = $cartItem['count'] * $needlePrice;
            $subTotal += $price;
            if ($cartItem['discount'] !== 0) {
                $discountTotal += $price - ($price * $cartItem['discount'] / 100);
            }
            $total += $subTotal - $discountTotal;
        }
        return [
            'subTotal' => $subTotal,
            'discountTotal' => $discountTotal,
            'total' => $total
        ];
    }

    /**
     * @throws Exception
     */
    public function addProductToCart(int $productId, int $count): array
    {
        $availableQuantity = $this->productBatchRepository->getProductQuantityInStore($productId, $this->store);
        $inCartCount = $this->getInCartProductCount($productId);

        $quantityDelta = $availableQuantity - $count - $inCartCount;

        if ($quantityDelta < 0) {
            throw new Exception('Недостаточно товара');
        }

        $this->cart->items()->updateOrCreate([
            'product_id' => $productId,
        ], [
            'count' => $inCartCount + $count,
        ]);

        return [
            'inCart' => $inCartCount + $count,
            'available' => $availableQuantity,
            'added' => $count
        ];
    }

    /**
     * @throws Exception
     */
    public function removeProductFromCart(int $productId): void
    {
        $item = $this->cart->items()->where('product_id', $productId)->first();

        if ($item) {
            $item->delete();
        } else {
            throw new Exception('Товар не найден в корзине');
        }
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
