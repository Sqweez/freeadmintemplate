<?php

namespace App\Actions\Arrival;

use App\Arrival;

class CreateArrivalAction {

    public function handle(array $payload): Arrival {
        $arrival = Arrival::create(\Arr::except($payload, 'products'));
        collect($payload['products'])->each(function ($product) use ($arrival) {
            $arrival->products()->create([
                'product_id' => $product['id'],
                'count' => $product['count'],
                'purchase_price' => $product['purchase_price']
            ]);
        });
        return $arrival;
    }
}
