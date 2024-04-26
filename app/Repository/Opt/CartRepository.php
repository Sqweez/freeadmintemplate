<?php

namespace App\Repository\Opt;

use App\Repository\ProductBatchRepository;
use App\Store;
use App\v2\Models\Product;
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
    private DailyProductsRepository $dailyProductsRepository;

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
        $this->dailyProductsRepository = app(DailyProductsRepository::class);
    }

    public function getCart(): array
    {
        // @TODO переписать на пайпы
        $products = $this->retrieveCartProducts();
        $result = $this->checkQuantities($products);
        $products = $result['products'];
        $stockChangedProducts = $result['stockChangedProducts'];
        $products = $this->checkPromotionDailyInactivity($products);
        $products = $this->applyPromotions($products);
        return [
            'products' => $products,
            'total' => $this->getTotal($products),
            'stockChangedProducts' => $stockChangedProducts
        ];
    }

    private function checkQuantities(Collection $products): array
    {
        $stockChangedProducts = collect([]);
        $products = $products->map(function ($product) use (&$stockChangedProducts, $products) {
            $stockQuantity = $product->product->batches->sum('quantity');
            $totalCartQuantity = $products->where('product_id', $product->product_id)->sum('count');
            if ($stockChangedProducts->where('product_id', $product->product_id)->count()) {
                return $product;
            }
            if ($stockQuantity < $totalCartQuantity) {
                $stockChangedProducts->push([
                    'id' => $product->id,
                    'product_id' => $product->product_id
                ]);
                $product->update([
                    'count' => min($stockQuantity, $totalCartQuantity),
                ]);
            }
            return $product;
        });
        return [
            'products' => $products,
            'stockChangedProducts' => $stockChangedProducts,
        ];
    }

    private function checkPromotionDailyInactivity(Collection $products): Collection
    {
        $dailyProducts = $this->dailyProductsRepository->getProductIds();
        return $products->map(function ($product) use ($dailyProducts) {
            if ($product->discount === 100) {
                return $product;
            }
            $needleProduct = $dailyProducts->where('product_id', $product->product->product_id)->first();
            if (!$needleProduct) {
                $product->update(['discount' => 0]);
            }
            if ($needleProduct && $needleProduct->discount !== $product->discount) {
                $product->update([
                    'discount' => $needleProduct->discount
                ]);
            }
            return $product;
        });
    }

    private function retrieveCartProducts(): Collection
    {
        $storeTypeId = Store::whereTypeId(4)->select(['id'])->value('id');

        return $this->cart->items()->where('count', '>', 0)->with([
            'product.product' => function ($query) use ($storeTypeId) {
                $query->with([
                    'wholesale_prices',
                    'manufacturer:id,manufacturer_name',
                    'product_thumbs',
                    'attributes',
                ])
                ->select('id', 'product_name', 'category_id', 'manufacturer_id');
            },
            'product' => function ($query) use ($storeTypeId) {
                $query->with([
                    'batches' => function ($query) use ($storeTypeId) {
                        $query
                            ->where('store_id', $storeTypeId)
                            ->where('quantity', '>', 0);
                    },
                    'attributes'
                ]);
            }
        ])
        ->get();
    }

    public function getTotal(Collection $products): array
    {
        $subTotal = 0;
        $discountTotal = 0;
        /* @var UserCartItem $product */
        foreach ($products as $product) {
            $needlePrice = $product->product->product->wholesale_prices->where(
                'currency_id',
                $this->client->preferred_currency_id
            )->first()->price;
            $price = $product->count * $needlePrice;
            $subTotal += $price;
            if ($product->discount !== 0) {
                $discountAmount = $price * ($product->discount / 100);
                $discountTotal += $discountAmount;
            }
        }
        $total = $subTotal - $discountTotal;
        $discountPercentByTotal = $this->calculateDiscountByTotal($total);
        $this->cart->update([
            'discount' => $discountPercentByTotal
        ]);
        $discountTotal += $total * ($discountPercentByTotal / 100);
        $total = $total * (1 - $discountPercentByTotal / 100);
        return [
            'subTotal' => $subTotal,
            'discountTotal' => $discountTotal,
            'total' => $total,
            'itemsTotal' => $products->sum('count'),
            'specialMessage' => $this->getSpecialMessage($total),
            'notifications' => $this->getNotifications()
        ];
    }

    private function applyPromotions(Collection $products): Collection
    {
        $toDelete = [];
        $toUpdate = [];
        $nomadProducts = $products
            ->where('product.product.manufacturer_id', __hardcoded(608))
            ->groupBy('product.product_id');
        foreach ($nomadProducts as $key => $items) {
            $totalCount = $items->sum('count');
            $freeItemCount = $items->where('discount', 100)->sum('count');
            $requiredItemCount = intdiv($totalCount, 8);
            $neededFreeItems = $requiredItemCount - $freeItemCount;
            $item = $items->where('discount', '<', 100)->last();
            if ($neededFreeItems > 0) {
                if ($freeItemCount > 0) {
                    $freeProduct = $items->where('discount', 100)->first();
                    $freeProduct->increment('count', $neededFreeItems);
                    $toUpdate[] = ['id' => $freeProduct->id, 'delta' => $neededFreeItems];
                } else {
                    $freeProduct = $item->replicate();
                    $freeProduct->count = $neededFreeItems;
                    $freeProduct->discount = 100;
                    $freeProduct->save();
                    $products->push($freeProduct);
                }
                $item->decrement('count', $neededFreeItems);
                $toUpdate[] = ['id' => $item->id, 'delta' => $neededFreeItems * - 1];
            } elseif ($neededFreeItems < 0) {
                $freeProduct = $items->where('discount', 100)->first();
                if ($requiredItemCount === 0) {
                    $freeProduct->delete();
                    $toDelete[] = $freeProduct->id;
                } else {
                    $freeProduct->increment('count', $neededFreeItems);
                    $toUpdate[] = ['id' => $freeProduct->id, 'delta' => $neededFreeItems];
                }
            }
        }

        \Log::info('to_update', $toUpdate);
        \Log::info('to_delete', $toDelete);

        return $products
            ->reject(function ($product) use ($toDelete) {
                return in_array($product->id, $toDelete);
            });
    }

    public function old_getTotal(): array
    {
        $this->applyPromotions();
        $cartItems = $this->cart->items()->with('product')->get();
        $prices = WholesalePrice::query()->whereIn(
            'product_id',
            $cartItems->pluck('product.product_id')->toArray()
        )->where('currency_id', $this->client->preferred_currency_id)->get();
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
            $inCartSkuCount = UserCartItem::query()->where('cart_id', $this->cart->id)->whereIn(
                'product_id',
                $skus->pluck('id')->toArray()
            )->get()->sum('count');

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

    private function old_applyPromotions()
    {
        // NOMAD 7+1
        $products = $this->cart->items()->get();
        $products->load('product.product:id,manufacturer_id');
        $nomadProducts = $products->where('product.product.manufacturer_id', 608)->groupBy('product.product_id');
        foreach ($nomadProducts as $key => $items) {
            $totalCount = $items->sum('count'); // Исключаем уже бесплатные товары из подсчета
            $freeItemCount = $items->where('discount', 100)->sum('count');
            $requiredItemCount = intdiv($totalCount, 8);
            $neededFreeItems = $requiredItemCount - $freeItemCount; // Вычисляем сколько бесплатных товаров должно быть
            $item = $items->where('discount', '<', 100)->last();
            if ($neededFreeItems > 0) {
                if ($freeItemCount > 0) {
                    $freeProduct = $items->where('discount', 100)->first();
                    $freeProduct->increment('count', $neededFreeItems);
                } else {
                    $freeProduct = $item->replicate();
                    $freeProduct->count = $neededFreeItems;
                    $freeProduct->discount = 100;
                    $freeProduct->save();
                }
                $item->decrement('count', $neededFreeItems);
                \Log::info("Продукт ID: $key - Применена акция '7+1'. Всего бесплатных товаров: $neededFreeItems");
            } elseif ($neededFreeItems < 0) {
                $freeProduct = $items->where('discount', 100)->first();
                if ($requiredItemCount === 0) {
                    $freeProduct->delete();
                } else {
                    $freeProduct->increment('count', $neededFreeItems);
                }
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
    public function addProductToCart(int $productId, int $count, ?int $cartProductId = null): array
    {
        $availableQuantity = $this->productBatchRepository->getProductQuantityInStore($productId, $this->store);
        $inCartCount = $this->getInCartProductCount($productId);
        if ($count > 0) {
            $quantityDelta = $availableQuantity - $count - $inCartCount;
            if ($quantityDelta < 0) {
                throw new Exception('Недостаточно товара');
            }
        }

        $product = Product::whereHas('sku', function ($q) use ($productId) {
            return $q->whereKey($productId);
        })->first();
        $product->loadActiveDailyDeals();
        $discount = 0;
        if ($product->optDailyDeals) {
            $discount = $product->optDailyDeals->discount;
        }

        if ($cartProductId) {
            $item = UserCartItem::find($cartProductId);
            $item->count += $count;
            $item->save();
        } else {
            $cartItem = $this->cart->items()->where('product_id', $productId)->where('discount', $discount)->first();
            if ($cartItem) {
                $cartItem->increment('count', $count);
            } else {
                $this->cart->items()->create([
                    'product_id' => $productId,
                    'discount' => $discount,
                    'count' => $count
                ]);
            }
        }

        /*
                $this->cart->items()->updateOrCreate([
                    'product_id' => $productId,
                    'discount' => 0,
                ], [
                    'count' => $inCartCount + $count,
                ]);*/


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
        return Store::whereTypeId(4)->first();
    }
}
