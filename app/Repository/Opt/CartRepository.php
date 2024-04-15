<?php

namespace App\Repository\Opt;

use App\Repository\ProductBatchRepository;
use App\Store;
use App\v2\Models\UserCart;
use App\v2\Models\UserCartItem;
use App\v2\Models\WholesaleClient;
use App\v2\Models\WholesalePrice;
use Exception;
use Illuminate\Database\Eloquent\Collection;

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
            throw new Exception('Вы должны быть авторизованы, для просмотра этой страницы');
        }
        $this->cart = $this->retrieveCart();
        $this->store = $this->retrieveStore();
        $this->productBatchRepository = new ProductBatchRepository();
    }

    public function getCart(): Collection
    {
        $products = $this->cart
            ->items
            ->load(['product.product.wholesale_prices' => function ($q) {
                return $q->where('currency_id', $this->client->preferred_currency_id);
            }])
            ->load(['product.product.manufacturer:id,manufacturer_name'])
            ->load(['product.product.product_thumbs'])
            ->load(['product.product.attributes'])
            ->load(['product.attributes'])
            ->load(['product.batches' => function ($query) {
                return $query->where('store_id', Store::whereTypeId(4)->first()->id)->where('quantity', '>', 0);
            }]);


        return $products;
    }

    public function getTotal(): array
    {
        $this->applyPromotions();
        $cartItems = $this->cart->items()->with('product')->get();
        $prices = WholesalePrice::query()
            ->whereIn('product_id', $cartItems->pluck('product.product_id')->toArray())
            ->where('currency_id', $this->client->preferred_currency_id)
            ->get();
        $subTotal = 0;
        $discountTotal = 0;
        foreach ($cartItems as $cartItem) {
            $needlePrice = $prices->where('product_id', $cartItem['product']['product_id'])->first()->price;
            $price = $cartItem['count'] * $needlePrice;
            $subTotal += $price;

            if ($cartItem['discount'] !== 0) {
                $discountAmount = $price * ($cartItem['discount'] / 100);
                $discountTotal += $discountAmount;
            }
        }

        $total = $subTotal - $discountTotal;
        // @TODO Move to separate method
        $this->cart->update([
            'discount' => $this->calculateDiscountByTotal($total)
        ]);
        $this->cart->fresh();
        $discountTotal += $total * ($this->cart->discount / 100);
        $total = $total * (1 - $this->cart->discount / 100);
        return [
            'subTotal' => $subTotal,
            'discountTotal' => $discountTotal,
            'total' => $total,
            'itemsTotal' => $cartItems->sum('count'),
            'specialMessage' => $this->getSpecialMessage($total),
            'notifications' => $this->getNotifications()
        ];
    }

    private function getNotifications(): ?array
    {
        $latestItem = UserCartItem::where('cart_id', $this->cart->id)->latest('updated_at')->first();
        if (!$latestItem) {
            return null;
        }
        $latestItem->load('product.product:id,manufacturer_id,category_id');
        $notification = null;
        if ($latestItem->product->product->manufacturer_id === __hardcoded(608)) {
            $skus = $latestItem->product->relativeSku()->select(['id', 'product_id'])->get();
            $inCartSkuCount = UserCartItem::query()
                ->where('cart_id', $this->cart->id)
                ->whereIn('product_id', $skus->pluck('id')->toArray())
                ->get()
                ->sum('count');

            if ($inCartSkuCount % 6 === 0) {
                $notification = [
                    'title' => 'Акция 7+1 на Nomad Nutrition',
                    'text' => 'При покупке 7 позиций, 8-ая будет бесплатной',
                ];
            }
        }
        return $notification;
    }

    private function getSpecialMessage($total): array
    {
        $messages = [];
        // @TODO будет переработано в дальнейшем
        if ($total >= 30_000) {
            $messages[] = [
                'type' => 'info',
                'message' => 'Бесплатная доставка на первый заказ'
            ];
        }

        return $messages;
    }

    private function applyPromotions()
    {
        // NOMAD 7+1
        $products = $this->cart->items()->get();
        $products->load('product.product:id,manufacturer_id');
        $nomadProducts = $products->where('product.product.manufacturer_id', 608)->groupBy('product.product_id');
        foreach ($nomadProducts as $key => $items) {
            $totalCount = 0;
            foreach ($items as $item) {
                $totalCount += $item['count'];
            }
            if ($totalCount >= 8 && $totalCount % 8 === 0) {
                $sets = floor($totalCount / 8);
                $discount = $sets;  // Даем 1 бесплатный товар за каждые 8 купленных
                $item = UserCartItem::find($items->last()->id);
                $freeProduct = $item->replicate();
                $item->decrement('count');
                $freeProduct->count = 1;
                $freeProduct->discount = 100;
                $freeProduct->save();
                \Log::info("Продукт ID: $key - Применена акция '7+1'. Всего бесплатных товаров: $discount");
                \Log::info("Товар для обновления", $item->toArray());
            } else {
                \Log::info("Продукт ID: $key - Акция '7+1' не применена. Общее количество товаров: $totalCount");
            }
        }
    }

    private function calculateDiscountByTotal($total): int
    {
        if ($total >= 1_000_000) {
            return 15;
        }
        if ($total >= 500_000) {
            return 10;
        }
        if ($total >= 200_000) {
            return 5;
        }
        return 0;
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
    public function removeProductFromCart(?UserCartItem $item): void
    {
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
        return $this->client->cart()->with(['items'])->firstOrCreate([]);
    }

    private function retrieveStore()
    {
        return Store::whereTypeId(4)
            ->first();
    }
}
