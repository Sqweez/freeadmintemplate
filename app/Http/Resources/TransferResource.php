<?php

namespace App\Http\Resources;

use App\Product;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $batches = $this->batches;

        return [
            'id' => $this->id,
            'parent_store' => $this->parent_store->name,
            'child_store' => $this->child_store->name,
            'user' => 'Администратор',
            'product_count' => $batches->count(),
            'position_count' => $batches->groupBy('product_id')->count(),
            'total_cost' => $this->getTotalCost($batches->toArray($batches))
        ];
    }
    private function getTotalCost($products) {
        $_products = $this->groupProducts($products);
        return array_reduce($_products, function ($a, $c) {
            $price = Product::find($c['id'])->product_price;
            return $a + ($price * $c['count']);
        }, 0);
    }

    private function groupProducts($products) {
        $_products = [];
        foreach ( $products as $value ) {
            $_products[$value['product_id']][] = $value;
        }
        $result = [];

        foreach ($_products as $product) {
            array_push($result, [
                'count' => count($product),
                'id' => $product[0]['product_id'],
            ]);
        }

        return $result;
    }
}
