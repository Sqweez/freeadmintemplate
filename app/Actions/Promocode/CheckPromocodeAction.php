<?php

namespace App\Actions\Promocode;

use App\Category;
use App\Concerns\UseQuickBindings;
use App\Manufacturer;
use App\Promocode;

class CheckPromocodeAction {

    use UseQuickBindings;

    private $promocode;

    public function handle(string $promoCode, array $cart = []): array {
        $this->promocode = $this->getPromocode($promoCode);
        if (!$this->promocode) {
            return $this->returnError('Промокод не найден');
        }

        $checkPromocodeRules = $this->checkPromocodeRules($cart);

        if ($checkPromocodeRules !== true) {
            return $this->returnError($checkPromocodeRules);
        }

        $recalculatedCart = $this->recalculateCart($cart);

        return [
            'success' => true,
            'message' => 'Успех',
            'cart' => $recalculatedCart,
            'promocode' => $this->promocode->only(['id', 'promocode', 'discount', 'promocode_type_id', 'promocode_gifts', 'client_id']),
        ];
    }

    private function getPromocode($promoCode) {
        return Promocode::query()
            ->where('promocode', 'like', '%' . $promoCode . '%')
            ->active()
            ->first();
    }

    private function checkPromocodeRules ($cart = []) {
        if ($this->promocode->promocode_condition_id === 1) {
            return true;
        }

        if (in_array($this->promocode->promocode_condition_id, [2, 3, 4])) {
            $minTotal = intval($this->promocode['promocode_condition_payload']['min_total']);
            if ($this->promocode->promocode_condition_id === 2) {
                $cartTotal = collect($cart)->reduce(function ($a, $c) {
                    return $a + $c['count'] * $c['product_price'];
                }, 0);

                if ($cartTotal >= $minTotal) {
                    return true;
                } else {
                    return 'Сумма товаров в корзине должна превышать ' . $minTotal;
                }
            }
            if ($this->promocode->promocode_condition_id === 3) {
                $manufacturer = Manufacturer::find($this->promocode['promocode_condition_payload']['brand_id']);
                $cartTotal = collect($cart)
                    ->filter(function ($item) use ($manufacturer) {
                        return $item['manufacturer_id'] === $manufacturer->id;
                    })
                    ->values()
                    ->reduce(function ($a, $c) {
                        return $a + $c['count'] * $c['product_price'];
                    }, 0);

                if ($cartTotal >= $minTotal) {
                    return true;
                } else {
                    return 'Сумма товаров от производителя "' . $manufacturer->manufacturer_name . '" в корзине должна превышать ' . $minTotal;
                }
            }

            if ($this->promocode->promocode_condition_id === 4) {
                $category = Category::find($this->promocode['promocode_condition_payload']['category_id']);
                $cartTotal = collect($cart)
                    ->filter(function ($item) use ($category) {
                        return $item['category_id'] === $category->id;
                    })
                    ->values()
                    ->reduce(function ($a, $c) {
                        return $a + $c['count'] * $c['product_price'];
                    }, 0);

                if ($cartTotal >= $minTotal) {
                    return true;
                } else {
                    return 'Сумма товаров категории "' . $category->category_name . '" в корзине должна превышать ' . $minTotal;
                }
            }
        }

        if ($this->promocode->promocode_condition_id === 5) {
            $products = $this->promocode['promocode_condition_payload']['products'];
            $groupedCart = collect($cart)
                ->groupBy('product_id')
                ->map(function ($items, $product_id) {
                    return [
                        'product_id' => $product_id,
                        'count' => $items->reduce(function ($a, $c) {
                            return $a + $c['count'];
                        }, 0),
                    ];
                })
                ->values();

            foreach ($products as $key => $product) {
                $needle = collect($groupedCart)
                    ->where('product_id', $product['id'])
                    ->first();

                if (!$needle || $needle['count'] < $product['count']) {
                    $products[$key]['is_correct'] = false;
                } else {
                    $products[$key]['is_correct'] = true;
                }
            }

            $hasError = collect($products)
                    ->filter(function ($p) {
                        return $p['is_correct'] === false;
                    })
                    ->count() > 0;

            return !$hasError ?: 'Не все товары из условия были добавлены в корзину';
        }
    }

    private function recalculateCart(array $cart = []) {
        if ($this->promocode->promocode_type_id === __hardcoded(1)) {
            return $this->recalculatePercentageDiscount($cart);
        }
        if ($this->promocode->promocode_type_id === __hardcoded(2)) {
            return $this->recalculateFixedDiscount($cart);
        }

        if ($this->promocode->promocode_type_id === __hardcoded(3)) {
            return $cart;
        }

        if ($this->promocode->promocode_type_id === __hardcoded(4)) {
            return $this->recalculateCascadeDiscount($cart);
        }

        return $cart;
    }

    private function recalculatePercentageDiscount($cart) {
        if ($this->promocode->promocode_purpose_id === __hardcoded(1)) {
            return collect($cart)
                ->map(function ($item) {
                    $discount = max($item['discount'], $this->promocode->discount);
                    unset($item['discount']);
                    $item['discount'] = $discount;
                    $item['final_price'] = ceil($item['product_price'] - $item['product_price'] * $discount / 100);
                    return $item;
                });
        }

        if ($this->promocode->promocode_purpose_id === __hardcoded(2)) {
            return collect($cart)
                ->map(function ($item) {
                    if (in_array($item['product_id'], $this->promocode->promocode_purpose_payload)) {
                        $discount = max($item['discount'], $this->promocode->discount);
                        unset($item['discount']);
                        $item['discount'] = $discount;
                        $item['final_price'] = ceil($item['product_price'] - $item['product_price'] * $discount / 100);
                    }
                    return $item;
                });
        }

        if ($this->promocode->promocode_purpose_id === __hardcoded(3)) {
            return collect($cart)
                ->map(function ($item) {
                    if (in_array($item['category_id'], $this->promocode->promocode_purpose_payload)) {
                        $discount = max($item['discount'], $this->promocode->discount);
                        unset($item['discount']);
                        $item['discount'] = $discount;
                        $item['final_price'] = ceil($item['product_price'] - $item['product_price'] * $discount / 100);
                    }
                    return $item;
                });
        }

        if ($this->promocode->promocode_purpose_id === __hardcoded(4)) {
            return collect($cart)
                ->map(function ($item) {
                    if (in_array($item['manufacturer_id'], $this->promocode->promocode_purpose_payload)) {
                        $discount = max($item['discount'], $this->promocode->discount);
                        unset($item['discount']);
                        $item['discount'] = $discount;
                        $item['final_price'] = ceil($item['product_price'] - $item['product_price'] * $discount / 100);
                    }
                    return $item;
                });
        }

        return $cart;
    }

    private function recalculateCascadeDiscount($cart) {
        if ($this->promocode->promocode_cascade['type'] == __hardcoded(1)) {
            return $this->recalculateCascadeDiscountBySum($cart);
        }
        if ($this->promocode->promocode_cascade['type'] == __hardcoded(2)) {
            return $this->recalculateCascadeDiscountByPosition($cart);
        }
        return $cart;
    }

    private function recalculateCascadeDiscountBySum($cart) {
        $cartTotal = $this->getCartTotal($cart);
        $cascadePayload = collect($this->promocode->promocode_cascade['payload']);
        $cascadePayload = $cascadePayload->filter(function ($payload) use ($cartTotal) {
            return $cartTotal >= $payload['amount'];
        })->values();

        return $this->calculateCascadeWithDiscount($cascadePayload, $cart);
    }

    private function getCartTotal($cart) {
        $cartItems = $this->collectCartItemsByPromocodePurpose($cart);
        return collect($cartItems)->reduce(function ($a, $c) {
            return $a + $c['product_price'] * $c['count'];
        }, 0);
    }

    private function recalculateCascadeDiscountByPosition($cart) {
        $cartTotal = $this->getCartTotalCount($cart);
        $cascadePayload = collect($this->promocode->promocode_cascade['payload']);
        $cascadePayload = $cascadePayload->filter(function ($payload) use ($cartTotal) {
            return $cartTotal >= $payload['amount'];
        })->values();

        return $this->calculateCascadeWithDiscount($cascadePayload, $cart);
    }

    private function getCartTotalCount($cart) {
        $cartItems = $this->collectCartItemsByPromocodePurpose($cart);
        return collect($cartItems)->reduce(function ($a, $c) {
            return $a + $c['count'];
        }, 0);
    }

    private function recalculateFixedDiscount ($cart) {
        return $cart;
    }

    private function returnError($message): array {
        return [
            'success' => false,
            'message' => $message
        ];
    }

    /**
     * @param \Illuminate\Support\Collection $cascadePayload
     * @param $cart
     * @return \Illuminate\Support\Collection|mixed
     */
    private function calculateCascadeWithDiscount(\Illuminate\Support\Collection $cascadePayload, $cart) {
        if ($cascadePayload->count() === 0) {
            return $cart;
        }
        $discount = $cascadePayload->last()['discount'];

        if ($this->promocode->promocode_purpose_id === __hardcoded(1)) {
            return collect($cart)->map(function ($item) use ($discount) {
                    $discount = max($item['discount'], $discount);
                    unset($item['discount']);
                    $item['discount'] = $discount;
                    $item['final_price'] = ceil($item['product_price'] - $item['product_price'] * $discount / 100);
                    return $item;
                });
        }

        if ($this->promocode->promocode_purpose_id === __hardcoded(2)) {
            return collect($cart)->map(function ($item) use ($discount) {
                    if (in_array($item['product_id'], $this->promocode->promocode_purpose_payload)) {
                        $discount = max($item['discount'], $discount);
                        unset($item['discount']);
                        $item['discount'] = $discount;
                        $item['final_price'] = ceil($item['product_price'] - $item['product_price'] * $discount / 100);
                    }
                    return $item;
                });
        }

        if ($this->promocode->promocode_purpose_id === __hardcoded(3)) {
            return collect($cart)->map(function ($item) use ($discount) {
                    if (in_array($item['category_id'], $this->promocode->promocode_purpose_payload)) {
                        $discount = max($item['discount'], $discount);
                        unset($item['discount']);
                        $item['discount'] = $discount;
                        $item['final_price'] = ceil($item['product_price'] - $item['product_price'] * $discount / 100);
                    }
                    return $item;
                });
        }

        if ($this->promocode->promocode_purpose_id === __hardcoded(4)) {
            return collect($cart)->map(function ($item) use ($discount) {
                    if (in_array($item['manufacturer_id'], $this->promocode->promocode_purpose_payload)) {
                        $discount = max($item['discount'], $discount);
                        unset($item['discount']);
                        $item['discount'] = $discount;
                        $item['final_price'] = ceil($item['product_price'] - $item['product_price'] * $discount / 100);
                    }
                    return $item;
                });
        }

        return $cart;
    }

    /**
     * @param $cart
     * @return \Illuminate\Support\Collection
     */
    private function collectCartItemsByPromocodePurpose($cart): \Illuminate\Support\Collection {
        $cartItems = collect($cart);
        if ($this->promocode->promocode_purpose_id === __hardcoded(2)) {
            $cartItems = $cartItems->filter(function ($item) {
                    return in_array($item['product_id'], $this->promocode->promocode_purpose_payload);
                });
        }
        if ($this->promocode->promocode_purpose_id === __hardcoded(3)) {
            $cartItems = $cartItems->filter(function ($item) {
                    return in_array($item['category_id'], $this->promocode->promocode_purpose_payload);
                });
        }
        if ($this->promocode->promocode_purpose_id === __hardcoded(4)) {
            $cartItems = $cartItems->filter(function ($item) {
                    return in_array($item['manufacturer_id'], $this->promocode->promocode_purpose_payload);
                });
        }
        return $cartItems;
    }
}
