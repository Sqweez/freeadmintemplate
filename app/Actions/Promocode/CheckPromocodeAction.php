<?php

namespace App\Actions\Promocode;

use App\Concerns\UseQuickBindings;
use App\Promocode;

class CheckPromocodeAction {

    use UseQuickBindings;

    private Promocode $promocode;

    public function handle(string $promoCode, array $cart = []) {
        $this->promocode = $this->getPromocode($promoCode);
        if (!$this->promocode) {
            return $this->returnError('Промокод не найден');
        }
    }

    private function getPromocode($promoCode) {
        return Promocode::query()
            ->where('promocode', 'like', '%' . $promoCode . '%')
            ->active()
            ->first();
    }

    private function returnError($message): array {
        return [
            'success' => false,
            'message' => $message
        ];
    }
}
