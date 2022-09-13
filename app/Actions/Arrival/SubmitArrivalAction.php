<?php

namespace App\Actions\Arrival;

use App\Arrival;
use App\ProductBatch;
use App\v2\Models\Product;

class SubmitArrivalAction {

    private SyncArrivalProductPricesAction $syncAction;

    public function __construct(SyncArrivalProductPricesAction $action) {
        $this->syncAction = $action;
    }

    public function handle(array $payload, Arrival $arrival) {
        $arrival->products()->delete();
        collect($payload['products'])->each(function ($product) use ($arrival) {
            $this->createProductBatch($product, $arrival);
            $arrival->products()->create([
                'product_id' => $product['product_id'],
                'count' => $product['count'],
                'purchase_price' => $product['purchase_price']
            ]);
        });
        $arrival->complete($payload['comment'], $payload['payment_cost']);
        $this->syncAction->handle($payload['products']);
    }

    private function createProductBatch($product, Arrival $arrival) {
        ProductBatch::create([
            'product_id' => $product['product_id'],
            'quantity' => $product['count'],
            'store_id' => $arrival->store_id,
            'purchase_price' => $product['purchase_price'],
            'arrival_id' => $arrival->id,
        ]);
    }
}
