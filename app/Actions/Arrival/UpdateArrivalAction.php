<?php

namespace App\Actions\Arrival;

use App\Arrival;
use Illuminate\Support\Arr;

class UpdateArrivalAction {

    private SyncArrivalProductPricesAction $syncAction;

    public function __construct(SyncArrivalProductPricesAction $action) {
        $this->syncAction = $action;
    }


    public function handle(array $payload, Arrival $arrival) {
        $arrival->update(Arr::except($payload, 'products'));
        $this->updateProducts($payload['products'], $arrival);
        $this->syncAction->handle($payload['products']);
    }

    private function updateProducts(array $products, Arrival $arrival) {
        $arrival->products()->delete();
        collect($products)->each(function ($product) use ($arrival) {
            $arrival->products()->create([
                'product_id' => $product['product_id'],
                'purchase_price' => $product['purchase_price'],
                'count' => $product['count']
            ]);
        });
    }
}
