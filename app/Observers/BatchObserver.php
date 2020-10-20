<?php

namespace App\Observers;

use App\ProductQuantity;

class BatchObserver
{
    public function created($batch) {
        //$this->increaseQuantity($batch);
    }

    public function updated($batch) {
        //$this->decreaseQuantity($batch);
    }

    public function decreaseQuantity($batch) {
        $quantity = $batch->quantity;
        $product_id = $batch->product_id;
        $store_id = $batch->store_id;
        $product_quantity = ProductQuantity::where('store_id', $store_id)->where('product_id', $product_id)->first();
        if ($product_quantity) {
            $product_quantity->quantity += $quantity;
            $product_quantity->save();
        } else {
            ProductQuantity::create([
                'store_id' => $store_id,
                'product_id' => $product_id,
                'quantity' => $product_quantity,
            ]);
        }
    }

    private function increaseQuantity($batch) {
        $quantity = $batch->quantity;
        $product_id = $batch->product_id;
        $store_id = $batch->store_id;
        $product_quantity = ProductQuantity::where('store_id', $store_id)->where('product_id', $product_id)->first();
        if ($product_quantity) {
            $product_quantity->quantity += $quantity;
            $product_quantity->save();
        } else {
            ProductQuantity::create([
                'store_id' => $store_id,
                'product_id' => $product_id,
                'quantity' => $product_quantity,
            ]);
        }
    }
}
