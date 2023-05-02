<?php

namespace App\Actions\Promocode;

use App\Concerns\UseQuickBindings;
use App\Promocode;

class CheckPromocodeAction {

    use UseQuickBindings;

    private $promocode;

    public function handle(string $promoCode, array $cart = []) {
        $this->promocode = $this->getPromocode($promoCode);
        if (!$this->promocode) {
            return $this->returnError('Промокод не найден');
        }

        $checkPromocodeRules = $this->checkPromocodeRules($cart);

        if ($checkPromocodeRules !== true) {
            return $this->returnError($checkPromocodeRules);
        }
        return [
            'success' => true,
            'message' => 'Успех'
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
        if ($this->promocode->promocode_condition_id === 2) {

            $minTotal = intval($this->promocode['promocode_condition_payload']['min_total']);

            $cartTotal = collect($cart)->reduce(function ($a, $c) {
                return $a + $c['count'] * $c['product_price'];
            }, 0);

            if ($cartTotal >= $minTotal) {
                return true;
            } else {
                return 'Сумма товаров в корзине должна превышать ' . $minTotal;
            }
        }
    }

    private function returnError($message): array {
        return [
            'success' => false,
            'message' => $message
        ];
    }
}
